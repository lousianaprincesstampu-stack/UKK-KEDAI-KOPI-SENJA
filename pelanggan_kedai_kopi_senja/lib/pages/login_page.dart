import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'register_page.dart';
import 'customer_home_page.dart';
import '../services/api_service.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();

  final _nomorHpController = TextEditingController();
  final _passwordController = TextEditingController();

  bool _passwordTersembunyi = true;
  bool _sedangLogin = false;

  @override
  void dispose() {
    _nomorHpController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _sedangLogin = true;
    });

    try {
      final response = await ApiService.post(
        '/api/login',
        {
          'no_hp': _nomorHpController.text.trim(),
          'password': _passwordController.text,
        },
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200) {
        final prefs = await SharedPreferences.getInstance();

        await prefs.setString('token', data['token']);

        final customer = data['customer'];

        await prefs.setInt('customer_id', customer['id']);
        await prefs.setString('customer_nama', customer['nama']);
        await prefs.setString('customer_no_hp', customer['no_hp']);

        if (!mounted) return;

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => CustomerHomePage(
              namaPelanggan: customer['nama'],
            ),
          ),
        );
      } else {
        String pesan = data['message'] ?? 'Login gagal.';

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
          _sedangLogin = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: const Color(0xFFFFF8EF),
        title: const Text('Masuk'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 24),
                const Center(
                  child: Icon(
                    Icons.coffee_rounded,
                    size: 75,
                    color: Color(0xFF6F4E37),
                  ),
                ),
                const SizedBox(height: 24),
                const Center(
                  child: Text(
                    'Selamat Datang!',
                    style: TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF4E342E),
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                const Center(
                  child: Text(
                    'Silakan masuk ke akun pelanggan Anda.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Color(0xFF795548),
                    ),
                  ),
                ),
                const SizedBox(height: 36),
                const Text(
                  'Nomor HP',
                  style: TextStyle(fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _nomorHpController,
                  keyboardType: TextInputType.phone,
                  decoration: InputDecoration(
                    hintText: 'Contoh: 081234567890',
                    prefixIcon: const Icon(Icons.phone_android),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
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
                const SizedBox(height: 20),
                const Text(
                  'Password',
                  style: TextStyle(fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _passwordController,
                  obscureText: _passwordTersembunyi,
                  decoration: InputDecoration(
                    hintText: 'Masukkan password',
                    prefixIcon: const Icon(Icons.lock_outline),
                    suffixIcon: IconButton(
                      onPressed: () {
                        setState(() {
                          _passwordTersembunyi = !_passwordTersembunyi;
                        });
                      },
                      icon: Icon(
                        _passwordTersembunyi
                            ? Icons.visibility_off
                            : Icons.visibility,
                      ),
                    ),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
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
                const SizedBox(height: 28),
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton(
                    onPressed: _sedangLogin ? null : _login,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF6F4E37),
                      foregroundColor: Colors.white,
                    ),
                    child: _sedangLogin
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2,
                            ),
                          )
                        : const Text(
                            'Masuk',
                            style: TextStyle(fontSize: 16),
                          ),
                  ),
                ),
                const SizedBox(height: 24),
                Center(
                  child: TextButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const RegisterPage(),
                        ),
                      );
                    },
                    child: const Text(
                      'Belum punya akun? Daftar sekarang',
                      style: TextStyle(
                        color: Color(0xFF6F4E37),
                      ),
                    ),
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
