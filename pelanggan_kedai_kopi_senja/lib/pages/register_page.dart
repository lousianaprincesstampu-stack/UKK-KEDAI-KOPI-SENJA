import 'dart:convert';
import 'package:flutter/material.dart';
import 'login_page.dart';
import '../services/api_service.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();

  final _namaController = TextEditingController();
  final _nomorHpController = TextEditingController();
  final _passwordController = TextEditingController();
  final _konfirmasiPasswordController = TextEditingController();

  bool _passwordTersembunyi = true;
  bool _sedangDaftar = false;

  @override
  void dispose() {
    _namaController.dispose();
    _nomorHpController.dispose();
    _passwordController.dispose();
    _konfirmasiPasswordController.dispose();
    super.dispose();
  }

  Future<void> _daftar() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _sedangDaftar = true;
    });

    try {
      final response = await ApiService.post(
        '/api/register',
        {
          'nama': _namaController.text.trim(),
          'no_hp': _nomorHpController.text.trim(),
          'password': _passwordController.text,
          'password_confirmation':
              _konfirmasiPasswordController.text,
        },
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 201) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Pendaftaran berhasil! Silakan masuk.'),
          ),
        );

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => const LoginPage(),
          ),
        );
      } else {
        String pesan = data['message'] ?? 'Pendaftaran gagal.';

        final errors = data['errors'];

        if (errors is Map && errors.isNotEmpty) {
          final errorPertama = errors.values.first;

          if (errorPertama is List && errorPertama.isNotEmpty) {
            pesan = errorPertama.first.toString();
          }
        }

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(pesan)),
        );
      }
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal menghubungi server: $e'),
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _sedangDaftar = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Daftar Akun'),
        backgroundColor: const Color(0xFFFFF8EF),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Center(
                  child: Icon(
                    Icons.person_add_alt_1,
                    size: 70,
                    color: Color(0xFF6F4E37),
                  ),
                ),
                const SizedBox(height: 20),
                const Center(
                  child: Text(
                    'Buat Akun Baru',
                    style: TextStyle(
                      fontSize: 25,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF4E342E),
                    ),
                  ),
                ),
                const SizedBox(height: 30),
                const Text('Nama Lengkap'),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _namaController,
                  decoration: const InputDecoration(
                    hintText: 'Masukkan nama lengkap',
                    prefixIcon: Icon(Icons.person_outline),
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.trim().isEmpty) {
                      return 'Nama wajib diisi';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 18),
                const Text('Nomor HP'),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _nomorHpController,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(
                    hintText: 'Contoh: 081234567890',
                    prefixIcon: Icon(Icons.phone_android),
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Nomor HP wajib diisi';
                    }

                    if (!RegExp(r'^08[0-9]{8,11}$').hasMatch(value)) {
                      return 'Nomor HP harus 10–13 digit dan diawali 08';
                    }

                    return null;
                  },
                ),
                const SizedBox(height: 18),
                const Text('Password'),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _passwordController,
                  obscureText: _passwordTersembunyi,
                  decoration: InputDecoration(
                    hintText: 'Buat password',
                    prefixIcon: const Icon(Icons.lock_outline),
                    suffixIcon: IconButton(
                      onPressed: () {
                        setState(() {
                          _passwordTersembunyi =
                              !_passwordTersembunyi;
                        });
                      },
                      icon: Icon(
                        _passwordTersembunyi
                            ? Icons.visibility_off
                            : Icons.visibility,
                      ),
                    ),
                    border: const OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Password wajib diisi';
                    }
                    if (value.length < 8) {
                      return 'Password minimal 8 karakter';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 18),
                const Text('Konfirmasi Password'),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _konfirmasiPasswordController,
                  obscureText: _passwordTersembunyi,
                  decoration: const InputDecoration(
                    hintText: 'Ulangi password',
                    prefixIcon: Icon(Icons.lock_outline),
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Konfirmasi password wajib diisi';
                    }
                    if (value != _passwordController.text) {
                      return 'Konfirmasi password tidak sama';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 28),
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton(
                    onPressed: _sedangDaftar ? null : _daftar,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF6F4E37),
                      foregroundColor: Colors.white,
                    ),
                    child: _sedangDaftar
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2,
                            ),
                          )
                        : const Text('Daftar Sekarang'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
