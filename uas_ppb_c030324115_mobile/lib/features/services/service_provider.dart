import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../auth/auth_provider.dart';

// Provider untuk mengambil data list servis laptop (FutureProvider)
final fetchServicesProvider = FutureProvider.autoDispose<List<dynamic>>((
  ref,
) async {
  final dio = ref.watch(dioProvider);
  final response = await dio.get('/services');
  return response.data['data'];
});

// Provider untuk handles aksi create/store data servis
final serviceActionProvider = Provider((ref) {
  final dio = ref.watch(dioProvider);
  return ServiceAction(dio);
});

class ServiceAction {
  final Dio _dio;
  ServiceAction(this._dio);

  Future<bool> createService(
    String deviceName,
    String serialNumber,
    String complaints,
    String phoneNumber,
  ) async {
    try {
      final response = await _dio.post(
        '/services',
        data: {
          'device_name': deviceName,
          'serial_number': serialNumber,
          'complaints': complaints,
          'phone_number': phoneNumber,
        },
      );
      return response.statusCode == 201;
    } catch (_) {
      return false;
    }
  }
}
