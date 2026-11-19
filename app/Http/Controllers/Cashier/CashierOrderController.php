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
            ->where('status', true)
            ->orderBy('nama_menu')
            ->get()
            ->filter(function ($menu) {

                if (!$menu->recipe || $menu->recipe->recipeDetails->isEmpty()) {
                    return false;
                }

                foreach ($menu->recipe->recipeDetails as $detail) {

                    $bahan = $detail->bahan;

                    if (!$bahan) {
                        return false;
                    }

                    if ($bahan->stok < $detail->jumlah) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

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

        $pesanan = Order::findOrFail($id);

        if (
            ($pesanan->status_pesanan === 'Baru' &&
                $request->status_pesanan !== 'Diproses') ||
            ($pesanan->status_pesanan === 'Diproses' &&
                $request->status_pesanan !== 'Selesai') ||
            $pesanan->status_pesanan === 'Selesai'
        ) {
            return back()->with('error', 'Perubahan status pesanan tidak diperbolehkan.');
        }

        $pesanan->update([
            'status_pesanan' => $request->status_pesanan,
        ]);

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

            if ($pesanan->status_pesanan !== 'Baru') {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Pesanan hanya bisa dibatalkan jika statusnya masih Baru.'
                ]);
            }

            $pengeluaran = StockMovement::where('order_id', $pesanan->id)
                ->where('tipe', 'keluar')
                ->lockForUpdate()
                ->get();

            if ($pengeluaran->isEmpty()) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Riwayat pengeluaran bahan tidak ditemukan. Pesanan tidak dapat dibatalkan.'
                ]);
            }

            $pengeluaranPerBahan = $pengeluaran->groupBy('bahan_id');

            foreach ($pengeluaranPerBahan as $bahanId => $movements) {
                $bahan = Bahan::whereKey($bahanId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $jumlahDikembalikan = $movements->sum('jumlah');

                $bahan->increment('stok', $jumlahDikembalikan);

                StockMovement::create([
                    'bahan_id' => $bahanId,
                    'tipe' => 'masuk',
                    'jumlah' => $jumlahDikembalikan,
                    'keterangan' => 'Pengembalian bahan karena pembatalan pesanan ' . $pesanan->kode_pesanan,
                    'order_id' => $pesanan->id,
                    'user_id' => Auth::id(),
                ]);
            }

            $pesanan->update([
                'status_pesanan' => 'Dibatalkan'
            ]);

            return $pesanan;
        });

        app(MenuStockService::class)->perbaruiSemuaStatus();

        return redirect()
            ->route('cashier.orders.show', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibatalkan dan stok bahan telah dikembalikan.');
    }

}
