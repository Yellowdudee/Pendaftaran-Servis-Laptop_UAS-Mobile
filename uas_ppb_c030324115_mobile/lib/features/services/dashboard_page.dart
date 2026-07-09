import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'service_provider.dart'; // Sesuaikan path provider servis kamu
import '../auth/auth_provider.dart'; // Sesuaikan path auth provider untuk logout
import 'profile_page.dart';

class DashboardPage extends ConsumerStatefulWidget {
  const DashboardPage({super.key});

  @override
  ConsumerState<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends ConsumerState<DashboardPage> {
  final _formKey = GlobalKey<FormState>();
  final _deviceController = TextEditingController();
  final _snController = TextEditingController();
  final _complaintsController = TextEditingController();
  final _phoneController = TextEditingController();
  bool _isSubmitting = false;

  // Definisi Warna Tema Web Premium Dark
  static const backgroundColor = Color(0xFF090A15);
  static const cardColor = Color(0xFF111326);
  static const primaryColor = Color(0xFF7F3CFF);
  static const textPrimary = Colors.white;
  static const textSecondary = Color(0xFF8B8EA8);

  void _submitData() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isSubmitting = true);

      final action = ref.read(serviceActionProvider);
      final sukses = await action.createService(
        _deviceController.text,
        _snController.text,
        _complaintsController.text,
        _phoneController.text,
      );

      setState(() => _isSubmitting = false);

      if (sukses) {
        if (!mounted) return;
        Navigator.pop(context); // Tutup modal sheet setelah sukses
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Antrean berhasil diajukan!'),
            backgroundColor: Colors.green,
          ),
        );
        _deviceController.clear();
        _snController.clear();
        _complaintsController.clear();
        ref.invalidate(fetchServicesProvider); // Refresh list data
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Gagal menambahkan antrean'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  // Fungsi Helper Rupiah yang sudah kebal terhadap String desimal Laravel
  String _formatRupiah(dynamic nominal) {
    if (nominal == null) return 'Rp 0';

    // Jika data berupa String (misal: "150000.00"), kita buang bagian desimal di belakang titik
    String hargaStr = nominal.toString();
    if (hargaStr.contains('.')) {
      hargaStr = hargaStr.split('.')[0];
    }

    int? hargaInt = int.tryParse(hargaStr);
    if (hargaInt == null) return 'Rp 0';

    RegExp reg = RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))');
    String mathFunc(Match match) => '${match[1]}.';
    return 'Rp ${hargaInt.toString().replaceAllMapped(reg, mathFunc)}';
  }

  void _showDetailDialog(BuildContext context, dynamic item) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: cardColor,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      item['device_name'] ?? '-',
                      style: const TextStyle(
                        color: textPrimary,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  _buildStatusBadge(item['status'] ?? 'pending'),
                ],
              ),
              const Divider(color: textSecondary, height: 24),
              _buildDetailRow('No. Seri (S/N)', item['serial_number'] ?? '-'),
              const SizedBox(height: 12),
              _buildDetailRow('Detail Keluhan', item['complaints'] ?? '-'),
              const SizedBox(height: 12),
              _buildDetailRow(
                'Tanggal Masuk',
                item['created_at'] != null
                    ? item['created_at'].toString().substring(0, 10)
                    : '-',
              ),

              const SizedBox(height: 12),
              _buildDetailRow('No. Telp', item['phone_number'] ?? '-'),

              const SizedBox(height: 12),
              _buildDetailRow(
                'Biaya Servis',
                _formatRupiah(item['total_cost']),
              ),

              const SizedBox(height: 12),
              _buildDetailRow(
                'Catatan Teknisi',
                item['technician_notes'] ?? '-',
              ),

              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: TextButton(
                  onPressed: () => Navigator.pop(context),
                  style: TextButton.styleFrom(
                    backgroundColor: primaryColor,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: const Text(
                    'Tutup',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(color: textSecondary, fontSize: 12)),
        const SizedBox(height: 4),
        Text(
          value,
          style: const TextStyle(
            color: textPrimary,
            fontSize: 14,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  // Fungsi Memunculkan Modal Ajukan Perbaikan Baru
  void _showAddServiceModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: cardColor,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => Padding(
        padding: EdgeInsets.only(
          bottom: MediaQuery.of(
            context,
          ).viewInsets.bottom, // Supaya form naik saat keyboard muncul
          left: 20,
          right: 20,
          top: 20,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Center(
              child: Text(
                'Daftarkan Laptop Baru',
                style: TextStyle(
                  color: textPrimary,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
            const SizedBox(height: 20),
            Form(
              key: _formKey,
              child: Column(
                children: [
                  _buildTextField(
                    _deviceController,
                    'MERK / TIPE LAPTOP',
                    'Contoh: Asus ZenBook 14',
                  ),
                  const SizedBox(height: 14),
                  _buildTextField(
                    _snController,
                    'NOMOR SERI (S/N)',
                    'Contoh: SN209283182',
                  ),
                  const SizedBox(height: 14),
                  _buildTextField(
                    _phoneController,
                    'NOMOR TELEPON',
                    'Contoh: 081234567890',
                  ),
                  const SizedBox(height: 14),
                  _buildTextField(
                    _complaintsController,
                    'DETAIL KELUHAN / KERUSAKAN',
                    'Jelaskan kondisi secara detail...',
                    maxLines: 3,
                  ),
                  const SizedBox(height: 24),
                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: ElevatedButton(
                      onPressed: _isSubmitting ? null : _submitData,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: primaryColor,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        elevation: 0,
                      ),
                      child: _isSubmitting
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(
                                color: Colors.white,
                                strokeWidth: 2,
                              ),
                            )
                          : const Text(
                              'Ajukan Perbaikan',
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                    ),
                  ),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final servicesAsync = ref.watch(fetchServicesProvider);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        backgroundColor: cardColor,
        elevation: 0,
        title: const Row(
          children: [
            Icon(Icons.laptop_mac, color: primaryColor),
            SizedBox(width: 8),
            Text(
              'Servis Laptop',
              style: TextStyle(
                color: Color.fromRGBO(255, 255, 255, 1),
                fontWeight: FontWeight.bold,
                fontSize: 18,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.account_circle, size: 28, color: Color.fromRGBO(148, 146, 146, 1)),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const ProfilePage()),
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.redAccent),
            onPressed: () async {
              await ref.read(authProvider.notifier).logout();
              if (!mounted) return;
              Navigator.of(context).pushReplacementNamed('/login');
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ================= SECTION 1: STATS CARD (Atas Web) =================
            servicesAsync.when(
              data: (data) {
                int total = data.length;
                int pending = data
                    .where(
                      (e) =>
                          e['status'] == 'pending' || e['status'] == 'menunggu',
                    )
                    .length;
                int selesai = data
                    .where(
                      (e) =>
                          e['status'] == 'selesai' || e['status'] == 'diambil',
                    )
                    .length;

                return Row(
                  children: [
                    Expanded(
                      child: _buildStatCard(
                        'TOTAL SERVIS',
                        '$total Unit',
                        Icons.inventory_2_outlined,
                        primaryColor,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: _buildStatCard(
                        'PENDING',
                        '$pending Unit',
                        Icons.hourglass_empty,
                        Colors.amber,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: _buildStatCard(
                        'SELESAI',
                        '$selesai Unit',
                        Icons.check_circle_outline,
                        Colors.green,
                      ),
                    ),
                  ],
                );
              },
              loading: () => const Center(
                child: LinearProgressIndicator(color: primaryColor),
              ),
              error: (_, __) => const SizedBox(),
            ),
            const SizedBox(height: 20),

            // ================= SECTION 2: TOMBOL PEMICU MODAL FORM =================
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton.icon(
                onPressed: () => _showAddServiceModal(context),
                icon: const Icon(Icons.add, color: textPrimary),
                label: const Text(
                  'DAFTARKAN LAPTOP BARU',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1,
                  ),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: cardColor,
                  foregroundColor: primaryColor,
                  side: const BorderSide(color: primaryColor, width: 1),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  elevation: 0,
                ),
              ),
            ),
            const SizedBox(height: 24),

            // ================= SECTION 3: LIST RIWAYAT ANTRIAN =================
            const Text(
              'Riwayat Servis Laptop Anda',
              style: TextStyle(
                color: textPrimary,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 12),
            servicesAsync.when(
              data: (data) {
                if (data.isEmpty) {
                  return const Padding(
                    padding: EdgeInsets.symmetric(vertical: 40),
                    child: Center(
                      child: Text(
                        'Belum ada riwayat antrean.',
                        style: TextStyle(color: textSecondary),
                      ),
                    ),
                  );
                }
                return ListView.separated(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: data.length,
                  separatorBuilder: (context, index) =>
                      const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final item = data[index];
                    return Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: cardColor,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: textSecondary.withOpacity(0.05),
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Text(
                                  item['device_name'] ?? 'Unknown Device',
                                  style: const TextStyle(
                                    color: textPrimary,
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              _buildStatusBadge(item['status'] ?? 'pending'),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'S/N: ${item['serial_number'] ?? '-'}',
                            style: const TextStyle(
                              color: textSecondary,
                              fontSize: 13,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            'Keluhan: ${item['complaints'] ?? '-'}',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              color: textSecondary,
                              fontSize: 13,
                            ),
                          ),
                          const Divider(
                            color: backgroundColor,
                            height: 20,
                            thickness: 1,
                          ),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                item['created_at'] != null
                                    ? item['created_at'].toString().substring(
                                        0,
                                        10,
                                      )
                                    : '-',
                                style: const TextStyle(
                                  color: textSecondary,
                                  fontSize: 12,
                                ),
                              ),
                              SizedBox(
                                height: 32,
                                child: ElevatedButton(
                                  onPressed: () =>
                                      _showDetailDialog(context, item),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF1B1D3A),
                                    foregroundColor: Colors.white,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(6),
                                    ),
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 14,
                                    ),
                                    elevation: 0,
                                  ),
                                  child: const Text(
                                    'Lihat Detail',
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    );
                  },
                );
              },
              loading: () => const Center(
                child: CircularProgressIndicator(color: primaryColor),
              ),
              error: (err, __) => Center(
                child: Text(
                  'Gagal memuat data: $err',
                  style: const TextStyle(color: Colors.red),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Helper Widget: Kartu Statistik Atas
  Widget _buildStatCard(
    String title,
    String value,
    IconData icon,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(height: 8),
          Text(
            title,
            style: const TextStyle(
              color: textSecondary,
              fontSize: 9,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            value,
            style: const TextStyle(
              color: textPrimary,
              fontSize: 14,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  // Helper Widget: Input Form Gelap Premium di Modal
  Widget _buildTextField(
    TextEditingController controller,
    String label,
    String hint, {
    int maxLines = 1,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            color: textSecondary,
            fontSize: 11,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          maxLines: maxLines,
          style: const TextStyle(color: textPrimary, fontSize: 14),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: const TextStyle(color: Color(0xFF4A4D6B), fontSize: 13),
            fillColor: backgroundColor,
            filled: true,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 12,
              vertical: 12,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: BorderSide.none,
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: const BorderSide(color: primaryColor, width: 1),
            ),
          ),
          validator: (val) =>
              (val == null || val.isEmpty) ? 'Bidang ini wajib diisi' : null,
        ),
        const SizedBox(height: 10),
      ],
    );
  }

  // Helper Widget: Status Badge warna-warni penentu antrean
  Widget _buildStatusBadge(String status) {
    Color bg;
    Color text;
    String label = status.toUpperCase();

    if (label == 'SELESAI') {
      bg = Colors.green.withOpacity(0.1);
      text = Colors.green;
    } else if (label == 'PROSES') {
      bg = Colors.blue.withOpacity(0.1);
      text = Colors.blue;
    } else if (label == 'DIAMBIL') {
      bg = const Color.fromARGB(255, 80, 80, 80).withOpacity(0.1);
      text = const Color.fromARGB(255, 80, 80, 80);
    } else {
      bg = Colors.amber.withOpacity(0.1);
      text = Colors.amber;
      label = 'MENUNGGU';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(4),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: text,
          fontSize: 10,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}
