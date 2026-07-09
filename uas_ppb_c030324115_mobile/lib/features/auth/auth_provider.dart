import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/dio_client.dart';

final dioProvider = Provider((ref) => DioClient().dio);
final storageProvider = Provider((ref) => const FlutterSecureStorage());

class AuthState {
  final bool isAuthenticated;
  final String? errorMessage;
  final bool isLoading;

  AuthState({this.isAuthenticated = false, this.errorMessage, this.isLoading = false});

  AuthState copyWith({bool? isAuthenticated, String? errorMessage, bool? isLoading}) {
    return AuthState(
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      errorMessage: errorMessage ?? this.errorMessage,
      isLoading: isLoading ?? this.isLoading,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final Dio _dio;
  final FlutterSecureStorage _storage;

  AuthNotifier(this._dio, this._storage) : super(AuthState()) {
    _checkToken();
  }

  // Cek apakah user sudah login sebelumnya saat aplikasi dibuka
  void _checkToken() async {
    final token = await _storage.read(key: 'auth_token');
    if (token != null) {
      state = AuthState(isAuthenticated: true);
    }
  }

  // Fungsi Login ke Laravel Sanctum
  Future<bool> login(String email, String password) async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      final response = await _dio.post('/login', data: {
        'email': email,
        'password': password,
      });

      if (response.statusCode == 200) {
        final token = response.data['token'];
        await _storage.write(key: 'auth_token', value: token);
        state = AuthState(isAuthenticated: true);
        return true;
      }
    } on DioException catch (e) {
      final msg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan';
      state = state.copyWith(isLoading: false, errorMessage: msg);
    }
    return false;
  }

  // Fungsi Logout
  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } catch (_) {}
    await _storage.delete(key: 'auth_token');
    state = AuthState(isAuthenticated: false);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(ref.watch(dioProvider), ref.watch(storageProvider));
});