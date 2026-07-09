import 'package:flutter/material.dart';
import 'package:dio/dio.dart'; // Pastikan sudah mengimport dio
import '../../core/constants.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  bool _obscurePassword = true;
  bool _isLoading = false;

  // Warna Konsisten Sesuai Tema Web Premium Dark
  static const backgroundColor = Color(0xFF090A15);
  static const cardColor = Color(0xFF111326);
  static const primaryColor = Color(0xFF7F3CFF);
  static const textPrimary = Colors.white;
  static const textSecondary = Color(0xFF8B8EA8);

  // Inisialisasi Dio
  final Dio _dio = Dio(
    BaseOptions(
      baseUrl: AppConstants.baseUrl,
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 10),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ),
  );

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);

      try {
        final response = await _dio.post(
          '/register',
          data: {
            'name': _nameController.text,
            'email': _emailController.text,
            'password': _passwordController.text,
          },
        );

        setState(() => _isLoading = false);

        if (response.statusCode == 201) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Registrasi Berhasil! Silakan masuk.'),
              backgroundColor: Colors.green,
            ),
          );
          Navigator.pop(context);
        }
      } on DioException catch (e) {
        setState(() => _isLoading = false);

        String errorMessage =
            e.response?.data['message'] ?? 'Terjadi kesalahan sistem';

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(errorMessage), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
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
                    const Icon(
                      Icons.person_add_outlined,
                      size: 64,
                      color: primaryColor,
                    ),
                    const SizedBox(height: 16),
                    const Text(
                      'Daftar Akun Baru',
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Silakan lengkapi data diri Anda',
                      style: TextStyle(color: textSecondary, fontSize: 13),
                    ),
                    const SizedBox(height: 32),

                    // Input Nama
                    _buildInputField(
                      controller: _nameController,
                      label: 'Nama Lengkap',
                      icon: Icons.person_outline,
                      validator: (value) => (value == null || value.isEmpty)
                          ? 'Nama tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 16),

                    // Input Email
                    _buildInputField(
                      controller: _emailController,
                      label: 'Email',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                      validator: (value) =>
                          (value == null || !value.contains('@'))
                          ? 'Masukkan email yang valid'
                          : null,
                    ),
                    const SizedBox(height: 16),

                    // Input Password
                    _buildInputField(
                      controller: _passwordController,
                      label: 'Password (Min 8 Karakter)',
                      icon: Icons.lock_outline,
                      isPassword: true,
                      obscureText: _obscurePassword,
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscurePassword
                              ? Icons.visibility_off_outlined
                              : Icons.visibility_outlined,
                          color: textSecondary,
                        ),
                        onPressed: () => setState(
                          () => _obscurePassword = !_obscurePassword,
                        ),
                      ),
                      validator: (value) => (value == null || value.length < 8)
                          ? 'Password minimal 8 karakter'
                          : null,
                    ),
                    const SizedBox(height: 16),

                    // Input Konfirmasi Password
                    _buildInputField(
                      controller: _confirmPasswordController,
                      label: 'Konfirmasi Password',
                      icon: Icons.lock_clock_outlined,
                      isPassword: true,
                      obscureText: _obscurePassword,
                      validator: (value) {
                        if (value != _passwordController.text) {
                          return 'Password konfirmasi tidak cocok';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 32),

                    // Tombol Daftar
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: _isLoading ? null : _handleRegister,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: primaryColor,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          elevation: 0,
                        ),
                        child: _isLoading
                            ? const SizedBox(
                                width: 24,
                                height: 24,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              )
                            : const Text(
                                'Daftar Sekarang',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Navigasi Kembali ke Login
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Text(
                          'Sudah punya akun?',
                          style: TextStyle(color: textSecondary),
                        ),
                        TextButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text(
                            'Login',
                            style: TextStyle(
                              color: primaryColor,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],
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

  // Helper Widget: TextField yang identik dengan gaya LoginPage kamu
  Widget _buildInputField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool isPassword = false,
    bool obscureText = false,
    Widget? suffixIcon,
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      obscureText: isPassword && obscureText,
      keyboardType: keyboardType,
      style: const TextStyle(color: textPrimary, fontSize: 14),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: textSecondary, fontSize: 13),
        prefixIcon: Icon(icon, color: textSecondary),
        suffixIcon: suffixIcon,
        filled: true,
        fillColor: backgroundColor,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 16,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 1),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Colors.redAccent, width: 1),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Colors.redAccent, width: 1),
        ),
      ),
      validator: validator,
    );
  }
}
