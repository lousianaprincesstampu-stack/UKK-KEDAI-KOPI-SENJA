<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Bahan;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\MenuStockService;

class CustomerOrderController extends Controller
{

    public function index(Request $request)
    {
        $pesanan = Order::with(['cafeTable', 'orderDetails'])
            ->where('customer_id', $request->user()->id)
            ->orderByDesc('tanggal_pesanan')
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'kode_pesanan' => $order->kode_pesanan,
                'nomor_meja' => $order->cafeTable?->nomor_meja,
                'tanggal_pesanan' => $order->tanggal_pesanan,
                'status_pesanan' => $order->status_pesanan,
                'status_pembayaran' => $order->status_pembayaran,
                'metode_pembayaran' => $order->metode_pembayaran,
                'total' => $order->total,
                'items' => $order->orderDetails->map(fn ($detail) => [
                    'nama_menu' => $detail->nama_menu,
                    'jumlah' => $detail->jumlah,
                    'harga' => $detail->harga,
                    'subtotal' => $detail->subtotal,
                ]),
            ]);

        return response()->json([
            'message' => 'Riwayat pesanan berhasil diambil.',
            'orders' => $pesanan,
        ]);
    }

    public function show(Request $request, $id)
    {
        $pesanan = Order::with(['cafeTable', 'orderDetails'])
            ->where('customer_id', $request->user()->id)
            ->find($id);

        if (!$pesanan) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pesanan berhasil diambil.',
            'order' => [
                'id' => $pesanan->id,
                'kode_pesanan' => $pesanan->kode_pesanan,
                'nomor_meja' => $pesanan->cafeTable?->nomor_meja,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->status_pembayaran,
                'metode_pembayaran' => $pesanan->metode_pembayaran,
                'subtotal' => $pesanan->subtotal,
                'service' => $pesanan->service,
                'pajak' => $pesanan->pajak,
                'total' => $pesanan->total,
                'items' => $pesanan->orderDetails->map(fn ($detail) => [
                    'menu_id' => $detail->menu_id,
                    'nama_menu' => $detail->nama_menu,
                    'jumlah' => $detail->jumlah,
                    'harga' => $detail->harga,
                    'subtotal' => $detail->subtotal,
                ]),
            ],
        ]);
    }

    public function receipt(Request $request, $id)
    {
        $pesanan = Order::with(['cafeTable', 'orderDetails'])
            ->where('customer_id', $request->user()->id)
            ->find($id);

        if (!$pesanan) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Struk pesanan berhasil diambil.',
            'receipt' => [
                'kode_pesanan' => $pesanan->kode_pesanan,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'nomor_meja' => $pesanan->cafeTable?->nomor_meja,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->status_pembayaran,
                'metode_pembayaran' => $pesanan->metode_pembayaran,
                'items' => $pesanan->orderDetails->map(fn ($detail) => [
                    'nama_menu' => $detail->nama_menu,
                    'jumlah' => $detail->jumlah,
                    'harga' => $detail->harga,
                    'subtotal' => $detail->subtotal,
                ]),
                'subtotal' => $pesanan->subtotal,
                'service' => $pesanan->service,
                'pajak' => $pesanan->pajak,
                'total' => $pesanan->total,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'cafe_table_id' => 'required|exists:cafe_tables,id',
            'metode_pembayaran' => 'required|in:Tunai,QRIS',
        ]);

        $customer = $request->user();

        $pesanan = DB::transaction(function () use ($validated, $customer) {

            DB::table('customers')
                ->where('id', $customer->id)
                ->lockForUpdate()
                ->first();

            $pesananAktif = Order::where('customer_id', $customer->id)
                ->where('status_pesanan', 'Baru')
                ->lockForUpdate()
                ->exists();

            if ($pesananAktif) {
                throw ValidationException::withMessages([
                    'pesanan' => 'Kamu masih memiliki pesanan baru yang belum diproses.',
                ]);
            }

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
                    $bahanKurang[] = $bahan->nama_bahan ?? 'Bahan tidak ditemukan';
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
                'customer_id' => $customer->id,
                'cafe_table_id' => $validated['cafe_table_id'],
                'user_id' => null,
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
                    'user_id' => null,
                ]);
            }

            return $pesanan;
        });

        app(MenuStockService::class)->perbaruiSemuaStatus();

        $pesanan->load(['cafeTable', 'orderDetails']);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'order' => [
                'id' => $pesanan->id,
                'kode_pesanan' => $pesanan->kode_pesanan,
                'nomor_meja' => $pesanan->cafeTable?->nomor_meja,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->status_pembayaran,
                'metode_pembayaran' => $pesanan->metode_pembayaran,
                'subtotal' => $pesanan->subtotal,
                'service' => $pesanan->service,
                'pajak' => $pesanan->pajak,
                'total' => $pesanan->total,
                'items' => $pesanan->orderDetails->map(fn ($detail) => [
                    'menu_id' => $detail->menu_id,
                    'nama_menu' => $detail->nama_menu,
                    'jumlah' => $detail->jumlah,
                    'harga' => $detail->harga,
                    'subtotal' => $detail->subtotal,
                ]),
            ],
        ], 201);
    }
}

