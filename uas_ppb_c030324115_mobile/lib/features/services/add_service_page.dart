import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'service_provider.dart';

class AddServicePage extends ConsumerStatefulWidget {
  const AddServicePage({super.key});

  @override
  ConsumerState<AddServicePage> createState() => _AddServicePageState();
}

class _AddServicePageState extends ConsumerState<AddServicePage> {
  final _deviceController = TextEditingController();
  final _snController = TextEditingController();
  final _complaintsController = TextEditingController();
  final _phoneController = TextEditingController();
  bool _isLoading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Daftar Servis Baru')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _deviceController,
              decoration: const InputDecoration(labelText: 'Nama / Tipe Laptop', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _snController,
              decoration: const InputDecoration(labelText: 'Nomor Seri (S/N)', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _complaintsController,
              maxLines: 3,
              decoration: const InputDecoration(labelText: 'Keluhan Laptop', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 20),
            _isLoading
                ? const CircularProgressIndicator()
                : ElevatedButton(
                    style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                    onPressed: () async {
                      setState(() => _isLoading = true);
                      final res = await ref.read(serviceActionProvider).createService(
                            _deviceController.text,
                            _snController.text,
                            _complaintsController.text,
                            _phoneController.text,
                          );
                      setState(() => _isLoading = false);

                      if (res && mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Pendaftaran servis berhasil dikirim!')),
                        );
                        ref.refresh(fetchServicesProvider); // Refresh list dashboard
                        context.pop(); // Kembali ke dashboard
                      } else {
                        if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Gagal mengirim pendaftaran.')),
                          );
                        }
                      }
                    },
                    child: const Text('Kirim Pengajuan'),
                  ),
          ],
        ),
      ),
    );
  }
}