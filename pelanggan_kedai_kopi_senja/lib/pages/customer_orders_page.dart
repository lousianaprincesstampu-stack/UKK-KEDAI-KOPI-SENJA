import 'package:flutter/material.dart';
import '../services/api_service.dart';

class CustomerOrdersPage extends StatefulWidget {
  const CustomerOrdersPage({super.key});

  @override
  State<CustomerOrdersPage> createState() => _CustomerOrdersPageState();
}

class _CustomerOrdersPageState extends State<CustomerOrdersPage> {
  late Future<Map<String, dynamic>> _futurePesanan;

  @override
  void initState() {
    super.initState();
    _futurePesanan = ambilPesanan();
  }

  Future<Map<String, dynamic>> ambilPesanan() async {
    return await ApiService.getDenganToken('/api/orders');
  }

  Future<void> muatUlang() async {
    final future = ambilPesanan();

    setState(() {
      _futurePesanan = future;
    });

    await future;
  }

  String formatRupiah(dynamic nilai) {
    final angka = num.tryParse(nilai.toString())?.toInt() ?? 0;
    final teks = angka.toString();
    final hasil = StringBuffer();

    for (int i = 0; i < teks.length; i++) {
      if (i > 0 && (teks.length - i) % 3 == 0) {
        hasil.write('.');
      }
      hasil.write(teks[i]);
    }

    return 'Rp $hasil';
  }

  String formatTanggal(dynamic tanggal) {
    if (tanggal == null) return '-';

    final date = DateTime.tryParse(tanggal.toString());

    if (date == null) return tanggal.toString();

    final tanggalText = date.day.toString().padLeft(2, '0');
    final bulanText = date.month.toString().padLeft(2, '0');
    final tahunText = date.year.toString();
    final jam = date.hour.toString().padLeft(2, '0');
    final menit = date.minute.toString().padLeft(2, '0');

    return '$tanggalText-$bulanText-$tahunText $jam:$menit';
  }

  Color warnaStatus(String status) {
    switch (status.toLowerCase()) {
      case 'selesai':
        return Colors.green;
      case 'dibatalkan':
        return Colors.red;
      case 'diproses':
        return Colors.orange;
      case 'baru':
        return const Color(0xFF6F4E37);
      default:
        return Colors.grey;
    }
  }

  void bukaDetail(Map<String, dynamic> pesanan) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => CustomerOrderDetailPage(
          orderId: pesanan['id'],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFF8EF),
      appBar: AppBar(
        backgroundColor: const Color(0xFFFFF8EF),
        elevation: 0,
        iconTheme: const IconThemeData(
          color: Color(0xFF4E342E),
        ),
        title: const Text(
          'Riwayat Pesanan',
          style: TextStyle(
            color: Color(0xFF4E342E),
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _futurePesanan,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(
                color: Color(0xFF6F4E37),
              ),
            );
          }

          if (snapshot.hasError) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(25),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(
                      Icons.wifi_off,
                      size: 50,
                      color: Color(0xFF795548),
                    ),
                    const SizedBox(height: 12),
                    const Text(
                      'Gagal mengambil riwayat pesanan.',
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 12),
                    ElevatedButton(
                      onPressed: muatUlang,
                      child: const Text('Coba Lagi'),
                    ),
                  ],
                ),
              ),
            );
          }

          final data = snapshot.data ?? {};
          final daftarPesanan = data['orders'] is List
              ? List<dynamic>.from(data['orders'])
              : <dynamic>[];

          if (daftarPesanan.isEmpty) {
            return RefreshIndicator(
              onRefresh: muatUlang,
              color: const Color(0xFF6F4E37),
              child: ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: const [
                  SizedBox(height: 130),
                  Icon(
                    Icons.receipt_long,
                    size: 65,
                    color: Color(0xFFD7C5B5),
                  ),
                  SizedBox(height: 15),
                  Center(
                    child: Text(
                      'Belum ada riwayat pesanan.',
                      style: TextStyle(
                        fontSize: 15,
                        color: Color(0xFF795548),
                      ),
                    ),
                  ),
                  SizedBox(height: 8),
                  Center(
                    child: Text(
                      'Pesanan yang kamu buat akan muncul di sini.',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey,
                      ),
                    ),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: muatUlang,
            color: const Color(0xFF6F4E37),
            child: ListView.builder(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              itemCount: daftarPesanan.length,
              itemBuilder: (context, index) {
                final pesanan = Map<String, dynamic>.from(
                  daftarPesanan[index],
                );

                final status =
                    (pesanan['status_pesanan'] ?? '-').toString();

                final items = pesanan['items'] is List
                    ? List<dynamic>.from(pesanan['items'])
                    : <dynamic>[];

                return Card(
                  color: Colors.white,
                  elevation: 1,
                  margin: const EdgeInsets.only(bottom: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                    side: const BorderSide(
                      color: Color(0xFFE5D7CA),
                    ),
                  ),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(14),
                    onTap: () => bukaDetail(pesanan),
                    child: Padding(
                      padding: const EdgeInsets.all(15),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment:
                                MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Text(
                                  pesanan['kode_pesanan']?.toString() ?? '-',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 14,
                                    color: Color(0xFF4E342E),
                                  ),
                                ),
                              ),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 10,
                                  vertical: 5,
                                ),
                                decoration: BoxDecoration(
                                  color: warnaStatus(status).withOpacity(0.12),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  status,
                                  style: TextStyle(
                                    color: warnaStatus(status),
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(
                            formatTanggal(pesanan['tanggal_pesanan']),
                            style: const TextStyle(
                              color: Colors.grey,
                              fontSize: 12,
                            ),
                          ),
                          const Divider(
                            height: 22,
                            color: Color(0xFFE5D7CA),
                          ),
                          Row(
                            children: [
                              const Icon(
                                Icons.table_restaurant_outlined,
                                size: 17,
                                color: Color(0xFF795548),
                              ),
                              const SizedBox(width: 6),
                              Text(
                                'Meja ${pesanan['nomor_meja'] ?? '-'}',
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: Color(0xFF795548),
                                ),
                              ),
                              const Spacer(),
                              Text(
                                '${items.length} jenis menu',
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: Color(0xFF795548),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            mainAxisAlignment:
                                MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Total Pembayaran',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Color(0xFF795548),
                                ),
                              ),
                              Text(
                                formatRupiah(pesanan['total']),
                                style: const TextStyle(
                                  fontSize: 15,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF6F4E37),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 10),
                          const Align(
                            alignment: Alignment.centerRight,
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(
                                  'Lihat Detail',
                                  style: TextStyle(
                                    color: Color(0xFF6F4E37),
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                SizedBox(width: 4),
                                Icon(
                                  Icons.arrow_forward_ios,
                                  size: 12,
                                  color: Color(0xFF6F4E37),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}

class CustomerOrderDetailPage extends StatefulWidget {
  final dynamic orderId;

  const CustomerOrderDetailPage({
    super.key,
    required this.orderId,
  });

  @override
  State<CustomerOrderDetailPage> createState() =>
      _CustomerOrderDetailPageState();
}

class _CustomerOrderDetailPageState
    extends State<CustomerOrderDetailPage> {
  late Future<Map<String, dynamic>> _futureDetail;

  @override
  void initState() {
    super.initState();
    _futureDetail = ambilDetail();
  }

  Future<Map<String, dynamic>> ambilDetail() async {
    return await ApiService.getDenganToken(
      '/api/orders/${widget.orderId}',
    );
  }

  String formatRupiah(dynamic nilai) {
    final angka = num.tryParse(nilai.toString())?.toInt() ?? 0;
    final teks = angka.toString();
    final hasil = StringBuffer();

    for (int i = 0; i < teks.length; i++) {
      if (i > 0 && (teks.length - i) % 3 == 0) {
        hasil.write('.');
      }
      hasil.write(teks[i]);
    }

    return 'Rp $hasil';
  }

  String formatTanggal(dynamic tanggal) {
    if (tanggal == null) return '-';

    final date = DateTime.tryParse(tanggal.toString());
    if (date == null) return tanggal.toString();

    final hari = date.day.toString().padLeft(2, '0');
    final bulan = date.month.toString().padLeft(2, '0');
    final tahun = date.year;
    final jam = date.hour.toString().padLeft(2, '0');
    final menit = date.minute.toString().padLeft(2, '0');

    return '$hari-$bulan-$tahun $jam:$menit';
  }

  Widget barisInformasi(String label, String nilai) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              color: Colors.grey,
              fontSize: 12,
            ),
          ),
          const SizedBox(height: 5),
          Text(
            nilai,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: Color(0xFF4E342E),
            ),
          ),
        ],
      ),
    );
  }

  Widget barisBiaya(String label, dynamic nilai, {bool tebal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 13,
              fontWeight: tebal ? FontWeight.bold : FontWeight.normal,
              color: const Color(0xFF795548),
            ),
          ),
          Text(
            formatRupiah(nilai),
            style: TextStyle(
              fontSize: 13,
              fontWeight: tebal ? FontWeight.bold : FontWeight.normal,
              color: const Color(0xFF4E342E),
            ),
          ),
        ],
      ),
    );
  }

  Color warnaStatus(String status) {
    switch (status.toLowerCase()) {
      case 'selesai':
        return Colors.green;
      case 'dibatalkan':
        return Colors.red;
      case 'diproses':
        return Colors.orange;
      case 'baru':
        return const Color(0xFF6F4E37);
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFF8EF),
      appBar: AppBar(
        backgroundColor: const Color(0xFFFFF8EF),
        elevation: 0,
        iconTheme: const IconThemeData(
          color: Color(0xFF4E342E),
        ),
        title: const Text(
          'Detail Pesanan',
          style: TextStyle(
            color: Color(0xFF4E342E),
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _futureDetail,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(
                color: Color(0xFF6F4E37),
              ),
            );
          }

          if (snapshot.hasError) {
            return const Center(
              child: Text('Gagal mengambil detail pesanan.'),
            );
          }

          final data = snapshot.data ?? {};
          final order = data['order'];

          if (order == null) {
            return const Center(
              child: Text('Pesanan tidak ditemukan.'),
            );
          }

          final pesanan = Map<String, dynamic>.from(order);
          final items = pesanan['items'] is List
              ? List<dynamic>.from(pesanan['items'])
              : <dynamic>[];

          final status =
              (pesanan['status_pesanan'] ?? '-').toString();

          return ListView(
            padding: const EdgeInsets.all(18),
            children: [
              Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: const Color(0xFFE5D7CA),
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Detail Pesanan',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF4E342E),
                      ),
                    ),
                    const SizedBox(height: 5),
                    const Text(
                      'Informasi lengkap pesanan pelanggan.',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 20),
                    barisInformasi(
                      'No. Pesanan',
                      pesanan['kode_pesanan']?.toString() ?? '-',
                    ),
                    barisInformasi(
                      'Pelanggan',
                      pesanan['nama_pelanggan']?.toString() ?? '-',
                    ),
                    barisInformasi(
                      'Meja',
                      pesanan['nomor_meja']?.toString() ?? '-',
                    ),
                    barisInformasi(
                      'Waktu Pesanan',
                      formatTanggal(pesanan['tanggal_pesanan']),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(bottom: 15),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Status Pesanan',
                            style: TextStyle(
                              color: Colors.grey,
                              fontSize: 12,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 6,
                            ),
                            decoration: BoxDecoration(
                              color: warnaStatus(status).withOpacity(0.12),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              status,
                              style: TextStyle(
                                color: warnaStatus(status),
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    barisInformasi(
                      'Metode Pembayaran',
                      pesanan['metode_pembayaran']?.toString() ?? '-',
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),
              const Text(
                'Daftar Menu',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF4E342E),
                ),
              ),
              const SizedBox(height: 10),
              Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: const Color(0xFFE5D7CA),
                  ),
                ),
                child: Column(
                  children: [
                    ...items.map((item) {
                      final menu = Map<String, dynamic>.from(item);
                      final jumlah =
                          int.tryParse(menu['jumlah'].toString()) ?? 0;

                      return Padding(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    menu['nama_menu']?.toString() ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      color: Color(0xFF4E342E),
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    '$jumlah x ${formatRupiah(menu['harga'])}',
                                    style: const TextStyle(
                                      fontSize: 12,
                                      color: Color(0xFF795548),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Text(
                              formatRupiah(menu['subtotal']),
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF6F4E37),
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                    const Divider(
                      height: 22,
                      color: Color(0xFFE5D7CA),
                    ),
                    barisBiaya('Subtotal', pesanan['subtotal']),
                    barisBiaya('Service', pesanan['service']),
                    barisBiaya('Pajak', pesanan['pajak']),
                    const Divider(
                      height: 20,
                      color: Color(0xFFE5D7CA),
                    ),
                    barisBiaya(
                      'Total Pembayaran',
                      pesanan['total'],
                      tebal: true,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => CustomerReceiptPage(
                          orderId: widget.orderId,
                        ),
                      ),
                    );
                  },
                  label: const Text('Lihat Struk'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6F4E37),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class CustomerReceiptPage extends StatefulWidget {
  final dynamic orderId;

  const CustomerReceiptPage({
    super.key,
    required this.orderId,
  });

  @override
  State<CustomerReceiptPage> createState() =>
      _CustomerReceiptPageState();
}

class _CustomerReceiptPageState extends State<CustomerReceiptPage> {
  late Future<Map<String, dynamic>> _futureStruk;

  @override
  void initState() {
    super.initState();
    _futureStruk = ambilStruk();
  }

  Future<Map<String, dynamic>> ambilStruk() async {
    return await ApiService.getDenganToken(
      '/api/orders/${widget.orderId}/receipt',
    );
  }

  String formatRupiah(dynamic nilai) {
    final angka = num.tryParse(nilai.toString())?.toInt() ?? 0;
    final teks = angka.toString();
    final hasil = StringBuffer();

    for (int i = 0; i < teks.length; i++) {
      if (i > 0 && (teks.length - i) % 3 == 0) {
        hasil.write('.');
      }
      hasil.write(teks[i]);
    }

    return 'Rp $hasil';
  }

  String formatTanggal(dynamic tanggal) {
    if (tanggal == null) return '-';

    final date = DateTime.tryParse(tanggal.toString());

    if (date == null) return tanggal.toString();

    final hari = date.day.toString().padLeft(2, '0');
    final bulan = date.month.toString().padLeft(2, '0');
    final tahun = date.year;
    final jam = date.hour.toString().padLeft(2, '0');
    final menit = date.minute.toString().padLeft(2, '0');

    return '$hari-$bulan-$tahun $jam:$menit';
  }

  Widget barisStruk(String label, String nilai) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Text(
              label,
              style: const TextStyle(
                fontSize: 13,
                color: Color(0xFF795548),
              ),
            ),
          ),
          const SizedBox(width: 12),
          Flexible(
            child: Text(
              nilai,
              textAlign: TextAlign.right,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: Color(0xFF4E342E),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget barisBiaya(String label, dynamic nilai, {bool tebal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 13,
              fontWeight: tebal ? FontWeight.bold : FontWeight.normal,
              color: const Color(0xFF795548),
            ),
          ),
          Text(
            formatRupiah(nilai),
            style: TextStyle(
              fontSize: 13,
              fontWeight: tebal ? FontWeight.bold : FontWeight.normal,
              color: const Color(0xFF4E342E),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFF8EF),
      appBar: AppBar(
        backgroundColor: const Color(0xFFFFF8EF),
        elevation: 0,
        iconTheme: const IconThemeData(
          color: Color(0xFF4E342E),
        ),
        title: const Text(
          'Struk Pesanan',
          style: TextStyle(
            color: Color(0xFF4E342E),
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _futureStruk,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(
                color: Color(0xFF6F4E37),
              ),
            );
          }

          if (snapshot.hasError) {
            return const Center(
              child: Padding(
                padding: EdgeInsets.all(25),
                child: Text(
                  'Gagal mengambil struk pesanan.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: Color(0xFF795548),
                  ),
                ),
              ),
            );
          }

          final data = snapshot.data ?? {};
          final receiptData = data['receipt'];

          if (receiptData == null) {
            return const Center(
              child: Text('Struk pesanan tidak ditemukan.'),
            );
          }

          final struk = Map<String, dynamic>.from(receiptData);
          final items = struk['items'] is List
              ? List<dynamic>.from(struk['items'])
              : <dynamic>[];

          return ListView(
            padding: const EdgeInsets.all(18),
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: const Color(0xFFE5D7CA),
                  ),
                ),
                child: Column(
                  children: [
                    const Text(
                      'KEDAI KOPI SENJA',
                      style: TextStyle(
                        fontSize: 19,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1,
                        color: Color(0xFF4E342E),
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Struk Pesanan',
                      style: TextStyle(
                        fontSize: 13,
                        color: Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 18),
                    const Divider(
                      color: Color(0xFFE5D7CA),
                    ),
                    const SizedBox(height: 8),
                    barisStruk(
                      'No. Pesanan',
                      struk['kode_pesanan']?.toString() ?? '-',
                    ),
                    barisStruk(
                      'Pelanggan',
                      struk['nama_pelanggan']?.toString() ?? '-',
                    ),
                    barisStruk(
                      'Meja',
                      struk['nomor_meja']?.toString() ?? '-',
                    ),
                    barisStruk(
                      'Waktu Pesanan',
                      formatTanggal(struk['tanggal_pesanan']),
                    ),
                    barisStruk(
                      'Metode Pembayaran',
                      struk['metode_pembayaran']?.toString() ?? '-',
                    ),
                    const SizedBox(height: 8),
                    const Divider(
                      color: Color(0xFFE5D7CA),
                    ),
                    const SizedBox(height: 8),
                    const Align(
                      alignment: Alignment.centerLeft,
                      child: Text(
                        'Detail Menu',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF4E342E),
                        ),
                      ),
                    ),
                    const SizedBox(height: 10),
                    ...items.map((item) {
                      final menu = Map<String, dynamic>.from(item);
                      final jumlah =
                          int.tryParse(menu['jumlah'].toString()) ?? 0;

                      return Padding(
                        padding: const EdgeInsets.symmetric(vertical: 7),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment:
                                    CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    menu['nama_menu']?.toString() ?? '-',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.bold,
                                      color: Color(0xFF4E342E),
                                    ),
                                  ),
                                  const SizedBox(height: 3),
                                  Text(
                                    '$jumlah x ${formatRupiah(menu['harga'])}',
                                    style: const TextStyle(
                                      fontSize: 12,
                                      color: Color(0xFF795548),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: 10),
                            Text(
                              formatRupiah(menu['subtotal']),
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: Color(0xFF4E342E),
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                    const Divider(
                      color: Color(0xFFE5D7CA),
                    ),
                    const SizedBox(height: 5),
                    barisBiaya('Subtotal', struk['subtotal']),
                    barisBiaya('Service', struk['service']),
                    barisBiaya('Pajak', struk['pajak']),
                    const Divider(
                      color: Color(0xFFE5D7CA),
                    ),
                    const SizedBox(height: 5),
                    barisBiaya(
                      'TOTAL',
                      struk['total'],
                      tebal: true,
                    ),
                    const SizedBox(height: 18),
                    const Divider(
                      color: Color(0xFFE5D7CA),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Terima kasih telah memesan di',
                      style: TextStyle(
                        fontSize: 12,
                        color: Color(0xFF795548),
                      ),
                    ),
                    const SizedBox(height: 3),
                    const Text(
                      'Kedai Kopi Senja',
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF6F4E37),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () {
                    Navigator.pop(context);
                  },
                  icon: const Icon(Icons.arrow_back),
                  label: const Text('Kembali ke Detail Pesanan'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: const Color(0xFF6F4E37),
                    side: const BorderSide(
                      color: Color(0xFF6F4E37),
                    ),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
