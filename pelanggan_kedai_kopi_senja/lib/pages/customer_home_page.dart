import 'dart:convert';
import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'customer_orders_page.dart';
import 'customer_profile_page.dart';

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
  late Future<List<dynamic>> _futureMeja;

  bool _sedangCheckout = false;

  int? _mejaTerpilih;
  String _kataKunci = '';
  String _metodePembayaran = 'Tunai';

  final TextEditingController _pencarianController =
      TextEditingController();

  final Map<String, Map<String, dynamic>> _keranjang = {};

  @override
  void initState() {
    super.initState();
    _futureMenus = ambilMenu();
    _futureMeja = ambilMeja();
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

  Future<List<dynamic>> ambilMeja() async {
    final data = await ApiService.get('/api/tables');

    if (data['tables'] is List) {
      return data['tables'];
    }

    throw Exception('Data meja tidak ditemukan.');
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
        content: Text(
          '${menu['nama_menu']} ditambahkan ke keranjang',
        ),
        duration: const Duration(seconds: 1),
        backgroundColor: const Color(0xFF6F4E37),
      ),
    );
  }

  void ubahJumlahKeranjang(String id, int perubahan) {
    setState(() {
      if (!_keranjang.containsKey(id)) return;

      final jumlahBaru =
          (_keranjang[id]!['jumlah'] as int) + perubahan;

      if (jumlahBaru <= 0) {
        _keranjang.remove(id);
      } else {
        _keranjang[id]!['jumlah'] = jumlahBaru;
      }
    });
  }

  void hapusDariKeranjang(String id) {
    setState(() {
      _keranjang.remove(id);
    });
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
      builder: (modalContext) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            final daftarKeranjang = _keranjang.values.toList();

            num subtotal = 0;

            for (final item in daftarKeranjang) {
              final harga =
                  num.tryParse(item['harga_jual'].toString()) ?? 0;

              subtotal += harga * (item['jumlah'] as int);
            }

            return SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: ConstrainedBox(
                  constraints: BoxConstraints(
                    maxHeight:
                        MediaQuery.of(context).size.height * 0.85,
                  ),
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
                        const Padding(
                          padding: EdgeInsets.all(20),
                          child: Center(
                            child: Text('Keranjang masih kosong.'),
                          ),
                        )
                      else
                        Flexible(
                          child: ListView.separated(
                            shrinkWrap: true,
                            itemCount: daftarKeranjang.length,
                            separatorBuilder: (context, index) =>
                                const Divider(
                              color: Color(0xFFE5D7CA),
                            ),
                            itemBuilder: (context, index) {
                              final item = daftarKeranjang[index];
                              final id = item['menu_id'].toString();

                              final harga =
                                  num.tryParse(
                                        item['harga_jual'].toString(),
                                      ) ??
                                      0;

                              final jumlah = item['jumlah'] as int;

                              return Row(
                                children: [
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          item['nama_menu'].toString(),
                                          style: const TextStyle(
                                            fontWeight: FontWeight.bold,
                                            color: Color(0xFF4E342E),
                                          ),
                                        ),
                                        const SizedBox(height: 5),
                                        Text(
                                          formatRupiah(harga * jumlah),
                                          style: const TextStyle(
                                            color: Color(0xFF795548),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  IconButton(
                                    onPressed: () {
                                      ubahJumlahKeranjang(id, -1);
                                      setModalState(() {});
                                    },
                                    icon: const Icon(
                                      Icons.remove_circle_outline,
                                      color: Color(0xFF6F4E37),
                                    ),
                                  ),
                                  Text(
                                    jumlah.toString(),
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 15,
                                    ),
                                  ),
                                  IconButton(
                                    onPressed: () {
                                      ubahJumlahKeranjang(id, 1);
                                      setModalState(() {});
                                    },
                                    icon: const Icon(
                                      Icons.add_circle_outline,
                                      color: Color(0xFF6F4E37),
                                    ),
                                  ),
                                  IconButton(
                                    onPressed: () {
                                      hapusDariKeranjang(id);
                                      setModalState(() {});
                                    },
                                    icon: const Icon(
                                      Icons.delete_outline,
                                      color: Colors.red,
                                    ),
                                  ),
                                ],
                              );
                            },
                          ),
                        ),

                      const Divider(
                        color: Color(0xFFD7C5B5),
                      ),
                      const SizedBox(height: 15),

                      const Text(
                        'Pilih Nomor Meja',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF4E342E),
                        ),
                      ),
                      const SizedBox(height: 8),

                      FutureBuilder<List<dynamic>>(
                        future: _futureMeja,
                        builder: (context, snapshot) {
                          if (snapshot.connectionState ==
                              ConnectionState.waiting) {
                            return const Center(
                              child: CircularProgressIndicator(
                                color: Color(0xFF6F4E37),
                              ),
                            );
                          }

                          if (snapshot.hasError) {
                            return Text(
                              'Gagal mengambil daftar meja: '
                              '${snapshot.error}',
                            );
                          }

                          final daftarMeja = snapshot.data ?? [];

                          if (daftarMeja.isEmpty) {
                            return const Text(
                              'Belum ada meja tersedia.',
                            );
                          }

                          return DropdownButtonFormField<int>(
                            initialValue: _mejaTerpilih,
                            isExpanded: true,
                            decoration: InputDecoration(
                              hintText: 'Pilih meja',
                              filled: true,
                              fillColor: Colors.white,
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                            items: daftarMeja.map((meja) {
                              return DropdownMenuItem<int>(
                                value: int.parse(
                                  meja['id'].toString(),
                                ),
                                child: Text(
                                  'Meja ${meja['nomor_meja']}',
                                ),
                              );
                            }).toList(),
                            onChanged: (value) {
                              setState(() {
                                _mejaTerpilih = value;
                              });

                              setModalState(() {});
                            },
                          );
                        },
                      ),

                      const SizedBox(height: 15),

                      const Text(
                        'Metode Pembayaran',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF4E342E),
                        ),
                      ),
                      const SizedBox(height: 8),

                      DropdownButtonFormField<String>(
                        initialValue: _metodePembayaran,
                        isExpanded: true,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                        items: const [
                          DropdownMenuItem(
                            value: 'Tunai',
                            child: Text('Tunai'),
                          ),
                          DropdownMenuItem(
                            value: 'QRIS',
                            child: Text('QRIS'),
                          ),
                        ],
                        onChanged: (value) {
                          if (value != null) {
                            setState(() {
                              _metodePembayaran = value;
                            });

                            setModalState(() {});
                          }
                        },
                      ),

                      const SizedBox(height: 15),

                      Row(
                        mainAxisAlignment:
                            MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Subtotal',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF4E342E),
                            ),
                          ),
                          Text(
                            formatRupiah(subtotal),
                            style: const TextStyle(
                              fontSize: 17,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF6F4E37),
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 15),

                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _sedangCheckout
                              ? null
                              : () => prosesCheckout(modalContext),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF6F4E37),
                            foregroundColor: Colors.white,
                            disabledBackgroundColor: Colors.grey,
                            padding: const EdgeInsets.symmetric(
                              vertical: 14,
                            ),
                          ),
                          child: _sedangCheckout
                              ? const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    color: Colors.white,
                                    strokeWidth: 2,
                                  ),
                                )
                              : const Text('Checkout'),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  Future<void> prosesCheckout(BuildContext modalContext) async {
    if (_keranjang.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Keranjang masih kosong.'),
        ),
      );
      return;
    }

    if (_mejaTerpilih == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Silakan pilih nomor meja.'),
        ),
      );
      return;
    }

    setState(() {
      _sedangCheckout = true;
    });

    try {
      final response = await ApiService.postDenganToken(
        '/api/orders',
        {
          'items': _keranjang.values.map((item) {
            return {
              'menu_id': item['menu_id'],
              'jumlah': item['jumlah'],
            };
          }).toList(),
          'cafe_table_id': _mejaTerpilih,
          'metode_pembayaran': _metodePembayaran,
        },
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 201) {
        setState(() {
          _keranjang.clear();
          _mejaTerpilih = null;
          _metodePembayaran = 'Tunai';
        });

        Navigator.pop(modalContext);

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Pesanan berhasil dibuat!'),
            backgroundColor: Colors.green,
          ),
        );
      } else {
        final pesan =
            data['message'] ?? 'Pesanan gagal dibuat.';

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(pesan.toString()),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Terjadi kesalahan: $e'),
          backgroundColor: Colors.red,
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _sedangCheckout = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFF8EF),

      appBar: AppBar(
        automaticallyImplyLeading: false,
        backgroundColor: const Color(0xFFFFF8EF),
        elevation: 0,
        title: const Text(
          'KEDAI KOPI SENJA',
          style: TextStyle(
            fontSize: 17,
            fontWeight: FontWeight.bold,
            color: Color(0xFF4E342E),
          ),
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
                          contentPadding:
                              const EdgeInsets.symmetric(vertical: 13),
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
                        final statusStok = menu['status_stok']?.toString();

                        final tersedia = statusStok == 'Tersedia';

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
                                      borderRadius:
                                          BorderRadius.circular(8),
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
        currentIndex: 0,
        selectedItemColor: const Color(0xFF6F4E37),
        unselectedItemColor: Colors.grey,
        backgroundColor: Colors.white,
        onTap: (index) {
          if (index == 1) {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => const CustomerOrdersPage(),
              ),
            );
          } else if (index == 2) {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => const CustomerProfilePage(),
              ),
            );
          }
        },
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.coffee),
            label: 'Menu',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.receipt_long),
            label: 'Pesanan',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}
