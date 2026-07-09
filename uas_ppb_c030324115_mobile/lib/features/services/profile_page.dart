import 'package:flutter/material.dart';

class ProfilePage extends StatelessWidget {
  const ProfilePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F111A),
      appBar: AppBar(
        title: const Text('Profil Pengembang'),
        backgroundColor: const Color(0xFF161925),
        elevation: 0,
        centerTitle: true,

        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          color: Colors.indigoAccent,
          onPressed: () {
            Navigator.pop(context); // Fungsi untuk kembali ke dashboard
          },
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            const SizedBox(height: 20),
            // Foto / Avatar Pengembang
            Center(
              child: Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.indigoAccent, width: 3),
                ),
                child: const CircleAvatar(
                  radius: 60,
                  backgroundColor: Color(0xFF161925),
                  backgroundImage: AssetImage('assets/foto_bintal.jpeg'),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Nama & NIM
            const Text(
              'Dzaki Ahmad Andreaz',
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'TI-4B',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 8),

            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
              decoration: BoxDecoration(
                color: Colors.indigoAccent.withOpacity(0.2),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.indigoAccent),
              ),
              child: const Text(
                'C030324115',
                style: TextStyle(
                  color: Colors.indigoAccent,
                  fontWeight: FontWeight.bold,
                  fontSize: 14,
                ),
              ),
            ),
            const SizedBox(height: 24),

            // Section: Tentang Saya
            _buildSectionTitle('Tentang Saya'),
            _buildInfoCard(
              icon: Icons.info_outline,
              content:
                  'Mahasiswa D3 Teknik Informatika dengan minat utama pada Penetration Testing dan Cybersecurity (Offensive Security). Memiliki pemahaman dasar dalam pengujian keamanan jaringan dan sistem, serta pengalaman menggunakan tools seperti Nmap dan Wireshark untuk analisis dan scanning. Telah mengikuti pelatihan keamanan siber (HCIA-Security dan Digital Talent Scholarship) dan memiliki semangat tinggi untuk mengembangkan kemampuan dalam menemukan serta menganalisis celah keamanan sistem.',
            ),
            const SizedBox(height: 16),

            // Section: Keahlian / Minat
            _buildSectionTitle('Hobi'),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                _buildTechChip('Musik'),
                _buildTechChip('Game'),
                _buildTechChip('Videografi'),
              ],
            ),
            const SizedBox(height: 16),

            _buildSectionTitle('Nomor HP'),
            _buildInfoCard(
              icon: Icons.phone,
              content:
                  '0895338825330',
            ),

            const SizedBox(height: 16),

            _buildSectionTitle('Email'),
            _buildInfoCard(
              icon: Icons.email,
              content:
                  'dzakiahmadandreaz@gmail.com',
            ),

            const SizedBox(height: 40),
            // Footer Identitas Proyek
            const Text(
              'UAS Pemrograman Perangkat Bergerak © 2026',
              style: TextStyle(color: Colors.white30, fontSize: 12),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Padding(
        padding: const EdgeInsets.only(bottom: 10, left: 4),
        child: Text(
          title,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: Colors.indigoAccent,
          ),
        ),
      ),
    );
  }

  Widget _buildInfoCard({required IconData icon, required String content}) {
    return Card(
      color: const Color(0xFF161925),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: Colors.indigoAccent),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                content,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 14,
                  height: 1.4,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTechChip(String label) {
    return Chip(
      label: Text(
        label,
        style: const TextStyle(color: Colors.white, fontSize: 12),
      ),
      backgroundColor: const Color(0xFF161925),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: const BorderSide(color: Colors.white10),
      ),
    );
  }
}
