import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import 'login_page.dart';
import 'customer_home_page.dart';

class CustomerProfilePage extends StatefulWidget {
  final bool wajibGantiPassword;

  const CustomerProfilePage({
    super.key,
    this.wajibGantiPassword = false,
  });

  @override
  State<CustomerProfilePage> createState() =>
      _CustomerProfilePageState();
}

class _CustomerProfilePageState extends State<CustomerProfilePage> {
  late Future<Map<String, dynamic>> _futureProfil;

  final _passwordLamaController = TextEditingController();
  final _passwordBaruController = TextEditingController();
  final _konfirmasiController = TextEditingController();

  bool _sedangMenyimpan = false;
  bool _sedangLogout = false;

  @override
  void initState() {
    super.initState();
    _futureProfil = ambilProfil();
  }

  @override
  void dispose() {
    _passwordLamaController.dispose();
    _passwordBaruController.dispose();
    _konfirmasiController.dispose();
    super.dispose();
  }

  Future<Map<String, dynamic>> ambilProfil() async {
    final data = await ApiService.getDenganToken('/api/profile');
    final customer = data['customer'];

    if (customer['must_change_password'] == true &&
        !widget.wajibGantiPassword) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (!mounted) return;

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => const CustomerProfilePage(
              wajibGantiPassword: true,
            ),
          ),
        );
      });
    }

    return customer;
  }

  Future<void> gantiPassword() async {
    final passwordLama = _passwordLamaController.text;
    final passwordBaru = _passwordBaruController.text;
    final konfirmasi = _konfirmasiController.text;

    if (passwordLama.isEmpty ||
        passwordBaru.isEmpty ||
        konfirmasi.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Semua kolom wajib diisi.'),
        ),
      );
      return;
    }

    if (passwordBaru.length < 8) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Password baru minimal 8 karakter.'),
        ),
      );
      return;
    }

    if (passwordBaru != konfirmasi) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Konfirmasi password tidak cocok.'),
        ),
      );
      return;
    }

    if (passwordBaru == passwordLama) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Password baru harus berbeda dari password lama.'),
        ),
      );
      return;
    }

    setState(() {
      _sedangMenyimpan = true;
    });

    try {
      final response = await ApiService.putDenganToken(
        '/api/change-password',
        {
          'password_lama': passwordLama,
          'password_baru': passwordBaru,
          'password_baru_confirmation': konfirmasi,
        },
      );

      if (!mounted) return;

      final data = jsonDecode(response.body);

      if (response.statusCode == 200) {
        _passwordLamaController.clear();
        _passwordBaruController.clear();
        _konfirmasiController.clear();

        if (widget.wajibGantiPassword) {
          final prefs = await SharedPreferences.getInstance();
          final namaPelanggan =
              prefs.getString('customer_nama') ?? 'Pelanggan';

          if (!mounted) return;

          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(
              builder: (context) => CustomerHomePage(
                namaPelanggan: namaPelanggan,
              ),
            ),
            (route) => false,
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Password berhasil diubah.'),
              backgroundColor: Colors.green,
            ),
          );
        }
      } else {
        final pesan = data['message'] ?? 'Gagal mengubah password.';

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
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _sedangMenyimpan = false;
        });
      }
    }
  }

  Future<void> logout() async {
    final konfirmasi = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Apakah kamu yakin ingin keluar?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (konfirmasi != true) return;

    setState(() {
      _sedangLogout = true;
    });

    try {
      final response = await ApiService.postDenganToken(
        '/api/logout',
        {},
      );

      if (!mounted) return;

      if (response.statusCode == 200) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.remove('token');
        await prefs.remove('customer_id');
        await prefs.remove('customer_nama');
        await prefs.remove('customer_no_hp');

        if (!mounted) return;

        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(
            builder: (context) => const LoginPage(),
          ),
          (route) => false,
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Logout gagal: ${response.body}'),
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Terjadi kesalahan: $e'),
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _sedangLogout = false;
        });
      }
    }
  }

  Widget inputPassword(
    String label,
    TextEditingController controller,
  ) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextField(
        controller: controller,
        obscureText: true,
        decoration: InputDecoration(
          labelText: label,
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: !widget.wajibGantiPassword,
      child: Scaffold(
        backgroundColor: const Color(0xFFFFF8EF),
        appBar: AppBar(
          automaticallyImplyLeading: false,
          backgroundColor: const Color(0xFFFFF8EF),
          elevation: 0,
          title: Text(
            widget.wajibGantiPassword
                ? 'GANTI PASSWORD'
                : 'PROFIL SAYA',
            style: const TextStyle(
              fontSize: 17,
              fontWeight: FontWeight.bold,
              color: Color(0xFF4E342E),
            ),
          ),
        ),
        body: FutureBuilder<Map<String, dynamic>>(
          future: _futureProfil,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(
                child: CircularProgressIndicator(
                  color: Color(0xFF6F4E37),
                ),
              );
            }

            if (snapshot.hasError || !snapshot.hasData) {
              return const Center(
                child: Text('Gagal mengambil data profil.'),
              );
            }

            final profil = snapshot.data!;

            return SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (widget.wajibGantiPassword) ...[
                    const Icon(
                      Icons.lock_reset,
                      size: 55,
                      color: Color(0xFF6F4E37),
                    ),
                    const SizedBox(height: 15),
                    const Text(
                      'Ganti Password Sementara',
                      style: TextStyle(
                        fontSize: 21,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF4E342E),
                      ),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Password akun Anda telah direset oleh Kasir. '
                      'Silakan buat password baru sebelum melanjutkan.',
                      style: TextStyle(
                        color: Color(0xFF795548),
                      ),
                    ),
                  ] else ...[
                    Center(
                      child: CircleAvatar(
                        radius: 45,
                        backgroundColor: const Color(0xFF6F4E37),
                        child: const Icon(
                          Icons.person,
                          size: 48,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ],
                  const SizedBox(height: 20),
                  Card(
                    color: Colors.white,
                    child: Padding(
                      padding: const EdgeInsets.all(18),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Informasi Pelanggan',
                            style: TextStyle(
                              fontSize: 17,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF4E342E),
                            ),
                          ),
                          const SizedBox(height: 18),
                          const Text('Nama'),
                          const SizedBox(height: 5),
                          Text(
                            profil['nama']?.toString() ?? '-',
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const Divider(height: 25),
                          const Text('Nomor HP'),
                          const SizedBox(height: 5),
                          Text(
                            profil['no_hp']?.toString() ?? '-',
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 25),
                  Text(
                    widget.wajibGantiPassword
                        ? 'Buat Password Baru'
                        : 'Ganti Password',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF4E342E),
                    ),
                  ),
                  const SizedBox(height: 15),
                  inputPassword(
                    'Password Lama / Sementara',
                    _passwordLamaController,
                  ),
                  inputPassword(
                    'Password Baru',
                    _passwordBaruController,
                  ),
                  inputPassword(
                    'Konfirmasi Password Baru',
                    _konfirmasiController,
                  ),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed:
                          _sedangMenyimpan ? null : gantiPassword,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF6F4E37),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(
                          vertical: 14,
                        ),
                      ),
                      child: _sedangMenyimpan
                          ? const CircularProgressIndicator(
                              color: Colors.white,
                            )
                          : const Text('Simpan Password'),
                    ),
                  ),
                  if (!widget.wajibGantiPassword) ...[
                    const SizedBox(height: 30),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        onPressed: _sedangLogout ? null : logout,
                        icon: const Icon(
                          Icons.logout,
                          color: Colors.red,
                        ),
                        label: Text(
                          _sedangLogout ? 'Memproses...' : 'Logout',
                          style: const TextStyle(color: Colors.red),
                        ),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(
                            vertical: 14,
                          ),
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
