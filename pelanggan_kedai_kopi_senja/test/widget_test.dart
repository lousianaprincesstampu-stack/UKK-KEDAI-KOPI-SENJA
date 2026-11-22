import 'package:flutter_test/flutter_test.dart';
import 'package:pelanggan_kedai_kopi_senja/main.dart';

void main() {
  testWidgets('Aplikasi menampilkan halaman Welcome', (WidgetTester tester) async {
    await tester.pumpWidget(const MyApp());

    expect(find.text('Kedai Kopi Senja'), findsOneWidget);
    expect(find.text('Masuk'), findsOneWidget);
    expect(find.text('Daftar Akun'), findsOneWidget);
  });
}
