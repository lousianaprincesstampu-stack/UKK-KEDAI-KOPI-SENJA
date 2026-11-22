import 'package:flutter/material.dart';
import 'pages/welcome_page.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Kedai Kopi Senja',
      theme: ThemeData(
        useMaterial3: true,
        scaffoldBackgroundColor: const Color(0xFFFFF8EF),
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF6F4E37),
        ),
        fontFamily: 'Roboto',
      ),
      home: const WelcomePage(),
    );
  }
}
