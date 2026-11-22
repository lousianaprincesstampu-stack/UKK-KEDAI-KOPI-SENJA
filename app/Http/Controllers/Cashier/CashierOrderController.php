<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\CafeTable;
use App\Models\Bahan;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\MenuStockService;

class CashierOrderController extends Controller
{
    public function create()
    {
        $menus = Menu::with([
            'recipe.recipeDetails.bahan'
        ])
            ->orderBy('nama_menu')
            ->get();

        $menus->each(function ($menu) {

            $alasan = null;

            if (!$menu->status) {
                $alasan = 'Menu tidak aktif';
            } elseif ($menu->status_stok === 'Tidak Tersedia') {
                $alasan = 'Resep belum lengkap';
            } elseif (!$menu->recipe || $menu->recipe->recipeDetails->isEmpty()) {
                $alasan = 'Resep belum tersedia';
            } else {

                $kebutuhanBahan = [];

                foreach ($menu->recipe->recipeDetails as $detail) {

                    $bahan = $detail->bahan;

                    if (!$bahan) {
                        $alasan = 'Bahan tidak tersedia';
                        break;
                    }

                    $kebutuhanBahan[$bahan->id] =
                        ($kebutuhanBahan[$bahan->id] ?? 0) + (float) $detail->jumlah;
                }

                if ($alasan === null) {

                    foreach ($kebutuhanBahan as $bahanId => $jumlahDibutuhkan) {

                        $bahan = $menu->recipe->recipeDetails
                            ->firstWhere('bahan_id', $bahanId)
                            ->bahan;

                        if ((float) $bahan->stok < $jumlahDibutuhkan) {
                            $alasan = 'Stok bahan tidak mencukupi';
                            break;
                        }
                    }
                }
            }

            $menu->setAttribute('dapat_dipesan', $alasan === null);
            $menu->setAttribute('alasan_tidak_dapat_dipesan', $alasan);
        });

        $meja = CafeTable::orderBy('nomor_meja')->get();

        return view('cashier.orders.create', compact('menus', 'meja'));
    }

    public function storeOffline(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'cafe_table_id' => 'required|exists:cafe_tables,id',
            'metode_pembayaran' => 'required|in:Tunai,QRIS',
        ]);

        $pesanan = DB::transaction(function () use ($validated) {

            $menus = Menu::with('recipe.recipeDetails.bahan')
                ->whereIn('id', collect($validated['items'])->pluck('menu_id'))
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $kebutuhanBahan = [];
            $detailPesanan = [];

            foreach ($validated['items'] as $item) {

                $menu = $menus->get($item['menu_id']);

                if (!$menu || !$menu->status) {
                    throw ValidationException::withMessages([
                        'items' => 'Ada menu yang sudah tidak aktif.',
                    ]);
                }

                if ($menu->status_stok === 'Tidak Tersedia') {
                    throw ValidationException::withMessages([
                        'items' => "Menu {$menu->nama_menu} tidak tersedia karena resep belum lengkap.",
                    ]);
                }

                if (!$menu->recipe || $menu->recipe->recipeDetails->isEmpty()) {
                    throw ValidationException::withMessages([
                        'items' => "Menu {$menu->nama_menu} belum memiliki resep.",
                    ]);
                }

                $jumlah = (int) $item['jumlah'];
                $harga = (float) $menu->harga_jual;
                $hpp = 0;

                foreach ($menu->recipe->recipeDetails as $detail) {

                    $bahan = $detail->bahan;

                    if (!$bahan) {
                        throw ValidationException::withMessages([
                            'items' => "Bahan resep {$menu->nama_menu} tidak ditemukan.",
                        ]);
                    }

                    $jumlahBahan = (float) $detail->jumlah * $jumlah;

                    $kebutuhanBahan[$bahan->id] =
                        ($kebutuhanBahan[$bahan->id] ?? 0) + $jumlahBahan;

                    $hpp += (float) $detail->jumlah *
                        (float) $bahan->harga_per_satuan;
                }

                $subtotalMenu = $harga * $jumlah;
                $subtotal += $subtotalMenu;

                $detailPesanan[] = [
                    'menu_id' => $menu->id,
                    'nama_menu' => $menu->nama_menu,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'hpp' => $hpp,
                    'subtotal' => $subtotalMenu,
                ];
            }

            $bahans = Bahan::whereIn('id', array_keys($kebutuhanBahan))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $bahanKurang = [];

            foreach ($kebutuhanBahan as $bahanId => $jumlahDibutuhkan) {

                $bahan = $bahans->get($bahanId);

                if (!$bahan || (float) $bahan->stok < $jumlahDibutuhkan) {
                    $namaBahan = $bahan->nama_bahan ?? 'Bahan tidak ditemukan';

                    $bahanKurang[] = $namaBahan;
                }
            }

            if (!empty($bahanKurang)) {
                throw ValidationException::withMessages([
                    'items' => 'Stok tidak mencukupi untuk: ' .
                        implode(', ', $bahanKurang),
                ]);
            }

            $service = round($subtotal * 0.05, 2);
            $pajak = round(($subtotal + $service) * 0.10, 2);
            $total = round($subtotal + $service + $pajak, 2);

            $kodePesanan = 'PSN-' . now()->format('YmdHis') .
                '-' . strtoupper(Str::random(4));

            $pesanan = Order::create([
                'kode_pesanan' => $kodePesanan,
                'customer_id' => null,
                'cafe_table_id' => $validated['cafe_table_id'],
                'user_id' => Auth::id(),
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'Baru',
                'status_pembayaran' => 'Belum Dibayar',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'subtotal' => $subtotal,
                'service' => $service,
                'pajak' => $pajak,
                'total' => $total,
            ]);

            foreach ($detailPesanan as $detail) {
                $pesanan->orderDetails()->create($detail);
            }

            foreach ($kebutuhanBahan as $bahanId => $jumlahDibutuhkan) {

                $bahan = $bahans->get($bahanId);

                $bahan->decrement('stok', $jumlahDibutuhkan);

                StockMovement::create([
                    'bahan_id' => $bahanId,
                    'tipe' => 'keluar',
                    'jumlah' => $jumlahDibutuhkan,
                    'keterangan' => 'Penggunaan bahan untuk pesanan ' . $kodePesanan,
                    'order_id' => $pesanan->id,
                    'user_id' => Auth::id(),
                ]);
            }

            return $pesanan;
        });

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return redirect()
            ->route('cashier.orders.show', $pesanan->id)
            ->with('success', 'Pesanan offline berhasil disimpan.');
    }

    public function index()
    {
        $pesanan = Order::with(['customer', 'cafeTable'])
            ->whereIn('status_pesanan', ['Baru', 'Diproses'])
            ->orderByDesc('tanggal_pesanan')
            ->get();

        return view('cashier.orders.index', compact('pesanan'));
    }

    public function list(Request $request)
    {
        $query = Order::with(['customer', 'cafeTable']);

        if ($request->filled('cari')) {
            $query->where('kode_pesanan', 'like', '%' . $request->cari . '%');
        }

        if ($request->filled('status')) {
            $query->where('status_pesanan', $request->status);
        }

        $pesanan = $query->orderByDesc('tanggal_pesanan')->get();

        return view('cashier.orders.list', compact('pesanan'));
    }

    public function show($id)
    {
        $pesanan = Order::with([
            'customer',
            'cafeTable',
            'orderDetails'
        ])->findOrFail($id);

        return view('cashier.orders.show', compact('pesanan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pesanan' => 'required|in:Diproses,Selesai',
        ]);

        $pesanan = DB::transaction(function () use ($request, $id) {
            $pesanan = Order::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $transisiDiizinkan = [
                'Baru' => 'Diproses',
                'Diproses' => 'Selesai',
            ];

            if (
                !isset($transisiDiizinkan[$pesanan->status_pesanan]) ||
                $transisiDiizinkan[$pesanan->status_pesanan] !== $request->status_pesanan
            ) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Perubahan status pesanan tidak diperbolehkan.',
                ]);
            }

            $pesanan->update([
                'status_pesanan' => $request->status_pesanan,
            ]);

            return $pesanan;
        });

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return redirect()
            ->route('cashier.orders.show', $pesanan->id)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function cancel($id)
    {
        $pesanan = DB::transaction(function () use ($id) {
            $pesanan = Order::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($pesanan->status_pesanan, ['Baru', 'Diproses'])) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Pesanan ini tidak dapat dibatalkan.',
                ]);
            }

            if ($pesanan->status_pembayaran === 'Dibayar') {
                throw ValidationException::withMessages([
                    'status_pembayaran' => 'Pesanan yang sudah dibayar tidak dapat dibatalkan melalui proses ini.',
                ]);
            }

            $pengeluaranStok = StockMovement::where('order_id', $pesanan->id)
                ->where('tipe', 'keluar')
                ->lockForUpdate()
                ->get();

            foreach ($pengeluaranStok as $pengeluaran) {
                $bahan = Bahan::whereKey($pengeluaran->bahan_id)
                    ->lockForUpdate()
                    ->first();

                if (!$bahan) {
                    continue;
                }

                $bahan->increment('stok', $pengeluaran->jumlah);

                StockMovement::create([
                    'bahan_id' => $bahan->id,
                    'tipe' => 'masuk',
                    'jumlah' => $pengeluaran->jumlah,
                    'keterangan' => 'Pengembalian bahan dari pembatalan pesanan ' . $pesanan->kode_pesanan,
                    'order_id' => $pesanan->id,
                    'user_id' => Auth::id(),
                ]);
            }

            $pesanan->update([
                'status_pesanan' => 'Dibatalkan',
            ]);

            return $pesanan;
        });

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return redirect()
            ->route('cashier.orders.show', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibatalkan dan stok bahan dikembalikan.');
    }
}
