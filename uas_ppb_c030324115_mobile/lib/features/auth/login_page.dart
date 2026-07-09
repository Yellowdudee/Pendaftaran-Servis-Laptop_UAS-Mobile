import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'register_page.dart';
import '../services/dashboard_page.dart'; // Import utama untuk DashboardPage
import '../auth/auth_provider.dart';

class LoginPage extends ConsumerStatefulWidget {
  const LoginPage({super.key});

  @override
  ConsumerState<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends ConsumerState<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  // Warna Konsisten Sesuai Tema Web Premium Dark
  static const backgroundColor = Color(0xFF090A15);
  static const cardColor = Color(0xFF111326);
  static const primaryColor = Color(0xFF7F3CFF);
  static const textPrimary = Colors.white;
  static const textSecondary = Color(0xFF8B8EA8);

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final isLoading = authState.isLoading;

    return Scaffold(
      backgroundColor: backgroundColor,
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Card(
            color: cardColor,
            elevation: 8,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20),
            ),
            child: Padding(
              padding: const EdgeInsets.all(24.0),
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.laptop_mac, size: 64, color: primaryColor),
                    const SizedBox(height: 16),
                    const Text(
                      'Login Servis',
                      style: TextStyle(
                        color: textPrimary,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 32),

                    _buildInput(
                      _emailController,
                      'Email',
                      Icons.email_outlined,
                      false,
                    ),
                    const SizedBox(height: 16),
                    _buildInput(
                      _passwordController,
                      'Password',
                      Icons.lock_outline,
                      true,
                    ),

                    const SizedBox(height: 32),
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: primaryColor,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          elevation: 0,
                        ),
                        onPressed: isLoading ? null : _handleLogin,
                        child: isLoading
                            ? const SizedBox(
                                width: 24,
                                height: 24,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              )
                            : const Text(
                                'MASUK',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                              ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Tombol Register (const DIAPUS karena RegisterPage punya objek Dio)
                    TextButton(
                      onPressed: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const RegisterPage(),
                        ), // const di depan RegisterPage sudah aman dilepas jika error berlanjut, tapi di sini pakai const constructor bawaan widget
                      ),
                      child: const Text(
                        'Belum punya akun? Daftar sekarang',
                        style: TextStyle(color: textSecondary),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInput(
    TextEditingController controller,
    String label,
    IconData icon,
    bool isPassword,
  ) {
    return TextFormField(
      controller: controller,
      obscureText: isPassword && _obscurePassword,
      style: const TextStyle(color: textPrimary, fontSize: 14),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: textSecondary, fontSize: 13),
        prefixIcon: Icon(icon, color: textSecondary),
        suffixIcon: isPassword
            ? IconButton(
                icon: Icon(
                  _obscurePassword
                      ? Icons.visibility_off_outlined
                      : Icons.visibility_outlined,
                  color: textSecondary,
                ),
                onPressed: () =>
                    setState(() => _obscurePassword = !_obscurePassword),
              )
            : null,
        filled: true,
        fillColor: backgroundColor,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 1),
        ),
      ),
      validator: (val) =>
          (val == null || val.isEmpty) ? '$label tidak boleh kosong' : null,
    );
  }

  void _handleLogin() async {
    if (_formKey.currentState!.validate()) {
      bool sukses = await ref
          .read(authProvider.notifier)
          .login(_emailController.text, _passwordController.text);
      if (sukses) {
        if (!mounted) return;
        // const dilepas dari DashboardPage() karena ia mendengarkan provider dinamis
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const DashboardPage()),
        );
      }
    }
  }
}
