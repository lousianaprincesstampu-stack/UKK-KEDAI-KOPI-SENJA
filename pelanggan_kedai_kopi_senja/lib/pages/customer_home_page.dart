
import 'package:flutter/material.dart';
import '../services/api_service.dart';

class CustomerHomePage extends StatefulWidget {
  final String namaPelanggan;

  const CustomerHomePage({
    super.key,
    required this.namaPelanggan,
  });

  @override
  State<CustomerHomePage> createState() => _CustomerHomePageState();
}

class _CustomerHomePageState extends State<CustomerHomePage> {
  late Future<List<dynamic>> _futureMenus;

  final TextEditingController _pencarianController =
      TextEditingController();

  String _kataKunci = '';

  final Map<String, Map<String, dynamic>> _keranjang = {};

  @override
  void initState() {
    super.initState();
    _futureMenus = ambilMenu();
  }

  @override
  void dispose() {
    _pencarianController.dispose();
    super.dispose();
  }

  Future<List<dynamic>> ambilMenu() async {
    final data = await ApiService.get('/api/menus');

    if (data['menus'] is List) {
      return data['menus'];
    }

    throw Exception('Gagal mengambil daftar menu.');
  }

  Future<void> muatUlangMenu() async {
    final future = ambilMenu();

    setState(() {
      _futureMenus = future;
    });

    await future;
  }

  String formatRupiah(dynamic harga) {
    final nilai = num.tryParse(harga.toString())?.toInt() ?? 0;
    final angka = nilai.toString();
    final hasil = StringBuffer();

    for (int i = 0; i < angka.length; i++) {
      if (i > 0 && (angka.length - i) % 3 == 0) {
        hasil.write('.');
      }
      hasil.write(angka[i]);
    }

    return 'Rp $hasil';
  }

  void tambahKeranjang(Map<String, dynamic> menu) {
    final id = menu['id'].toString();

    setState(() {
      if (_keranjang.containsKey(id)) {
        _keranjang[id]!['jumlah']++;
      } else {
        _keranjang[id] = {
          'menu_id': menu['id'],
          'nama_menu': menu['nama_menu'],
          'harga_jual': menu['harga_jual'],
          'jumlah': 1,
        };
      }
    });

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('${menu['nama_menu']} ditambahkan ke keranjang'),
        duration: const Duration(seconds: 1),
        backgroundColor: const Color(0xFF6F4E37),
      ),
    );
  }

  void tampilkanKeranjang() {
    showModalBottomSheet(
      context: context,
      backgroundColor: const Color(0xFFFFF8EF),
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(
          top: Radius.circular(22),
        ),
      ),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            final daftarKeranjang = _keranjang.values.toList();

            return Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Center(
                    child: Text(
                      'Keranjang',
                      style: TextStyle(
                        fontSize: 21,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF4E342E),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  if (daftarKeranjang.isEmpty)
                    const Center(
                      child: Padding(
                        padding: EdgeInsets.all(20),
                        child: Text('Keranjang masih kosong.'),
                      ),
                    )
                  else
                    ...daftarKeranjang.map((item) {
                      return ListTile(
                        contentPadding: EdgeInsets.zero,
                        title: Text(item['nama_menu']),
                        subtitle: Text(
                          '${item['jumlah']} x ${formatRupiah(item['harga_jual'])}',
                        ),
                        trailing: Text(
                          formatRupiah(
                            (num.tryParse(
                                      item['harga_jual'].toString(),
                                    ) ??
                                    0) *
                                item['jumlah'],
                          ),
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      );
                    }),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () {
                        Navigator.pop(context);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF6F4E37),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                      ),
                      child: const Text('Kembali ke Menu'),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFF8EF),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        backgroundColor: const Color(0xFFFFF8EF),
        elevation: 0,
        title: const Row(
          children: [
            Icon(
              Icons.local_cafe,
              color: Color(0xFF6F4E37),
              size: 27,
            ),
            SizedBox(width: 10),
            Text(
              'KEDAI KOPI SENJA',
              style: TextStyle(
                fontSize: 17,
                fontWeight: FontWeight.bold,
                color: Color(0xFF4E342E),
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            onPressed: muatUlangMenu,
            icon: const Icon(
              Icons.refresh,
              color: Color(0xFF6F4E37),
            ),
            tooltip: 'Muat ulang menu',
          ),
        ],
      ),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: muatUlangMenu,
          color: const Color(0xFF6F4E37),
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.fromLTRB(20, 15, 20, 25),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Hai, ${widget.namaPelanggan}!',
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF4E342E),
                  ),
                ),
                const SizedBox(height: 5),
                const Text(
                  'Selamat datang',
                  style: TextStyle(
                    fontSize: 14,
                    color: Color(0xFF795548),
                  ),
                ),
                const SizedBox(height: 25),
                Row(
                  children: [
                    Expanded(
                      child: TextField(
                        controller: _pencarianController,
                        onChanged: (value) {
                          setState(() {
                            _kataKunci = value.toLowerCase();
                          });
                        },
                        decoration: InputDecoration(
                          hintText: 'Cari menu',
                          prefixIcon: const Icon(
                            Icons.search,
                            color: Color(0xFF795548),
                          ),
                          filled: true,
                          fillColor: Colors.white,
                          contentPadding: const EdgeInsets.symmetric(
                            vertical: 13,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: const BorderSide(
                              color: Color(0xFFD7C5B5),
                            ),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: const BorderSide(
                              color: Color(0xFFD7C5B5),
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: const BorderSide(
                              color: Color(0xFF6F4E37),
                              width: 1.5,
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Container(
                      decoration: BoxDecoration(
                        color: const Color(0xFF6F4E37),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: IconButton(
                        onPressed: tampilkanKeranjang,
                        icon: const Icon(
                          Icons.shopping_cart,
                          color: Colors.white,
                        ),
                        tooltip: 'Keranjang',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 22),
                FutureBuilder<List<dynamic>>(
                  future: _futureMenus,
                  builder: (context, snapshot) {
                    if (snapshot.connectionState ==
                        ConnectionState.waiting) {
                      return const Center(
                        child: Padding(
                          padding: EdgeInsets.all(35),
                          child: CircularProgressIndicator(
                            color: Color(0xFF6F4E37),
                          ),
                        ),
                      );
                    }

                    if (snapshot.hasError) {
                      return Center(
                        child: Column(
                          children: [
                            const Icon(
                              Icons.wifi_off,
                              size: 45,
                              color: Color(0xFF795548),
                            ),
                            const SizedBox(height: 12),
                            const Text('Gagal memuat menu.'),
                            const SizedBox(height: 10),
                            ElevatedButton(
                              onPressed: muatUlangMenu,
                              child: const Text('Coba Lagi'),
                            ),
                          ],
                        ),
                      );
                    }

                    final semuaMenu = snapshot.data ?? [];

                    final menus = semuaMenu.where((menu) {
                      final nama = (menu['nama_menu'] ?? '')
                          .toString()
                          .toLowerCase();

                      return nama.contains(_kataKunci);
                    }).toList();

                    if (menus.isEmpty) {
                      return const Center(
                        child: Padding(
                          padding: EdgeInsets.all(25),
                          child: Text('Menu tidak ditemukan.'),
                        ),
                      );
                    }

                    return Column(
                      children: menus.map((menu) {
                        final tersedia =
                            menu['status_stok'] != 'Tidak Tersedia';

                        return Container(
                          margin: const EdgeInsets.only(bottom: 14),
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 15,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: const Color(0xFFE5D7CA),
                            ),
                          ),
                          child: Row(
                            children: [
                              Container(
                                width: 48,
                                height: 48,
                                decoration: BoxDecoration(
                                  color: const Color(0xFFFFF0DF),
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: const Icon(
                                  Icons.local_cafe,
                                  size: 27,
                                  color: Color(0xFF6F4E37),
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment:
                                      CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      menu['nama_menu'] ?? '-',
                                      style: const TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.bold,
                                        color: Color(0xFF4E342E),
                                      ),
                                    ),
                                    const SizedBox(height: 5),
                                    Text(
                                      formatRupiah(menu['harga_jual']),
                                      style: const TextStyle(
                                        fontSize: 13,
                                        color: Color(0xFF795548),
                                      ),
                                    ),
                                    if (!tersedia)
                                      const Padding(
                                        padding: EdgeInsets.only(top: 4),
                                        child: Text(
                                          'Tidak Tersedia',
                                          style: TextStyle(
                                            color: Colors.red,
                                            fontSize: 11,
                                          ),
                                        ),
                                      ),
                                  ],
                                ),
                              ),
                              const SizedBox(width: 5),
                              SizedBox(
                                height: 34,
                                child: ElevatedButton(
                                  onPressed: tersedia
                                      ? () => tambahKeranjang(
                                            Map<String, dynamic>.from(menu),
                                          )
                                      : null,
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor:
                                        const Color(0xFF6F4E37),
                                    foregroundColor: Colors.white,
                                    disabledBackgroundColor:
                                        Colors.grey.shade300,
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 10,
                                    ),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                  ),
                                  child: const Text(
                                    'Tambah',
                                    style: TextStyle(fontSize: 12),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        );
                      }).toList(),
                    );
                  },
                ),
                const SizedBox(height: 8),
              ],
            ),
          ),
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 1,
        type: BottomNavigationBarType.fixed,
        backgroundColor: Colors.white,
        selectedItemColor: const Color(0xFF6F4E37),
        unselectedItemColor: Colors.grey,
        selectedFontSize: 11,
        unselectedFontSize: 11,
        onTap: (index) {
          if (index != 1) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('Halaman ini akan kita buat berikutnya.'),
              ),
            );
          }
        },
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home_outlined),
            label: 'Beranda',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.restaurant_menu),
            label: 'Menu',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.receipt_long),
            label: 'Pesanan',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person_outline),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}
