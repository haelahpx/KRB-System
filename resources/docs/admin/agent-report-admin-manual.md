# Agent Report — Panduan Admin

Fungsi: melihat kinerja agent, Average Handle Time (AHT), jumlah tiket per agent, dan mengunduh laporan.

Komponen halaman:
- AHT Summary: rata‑rata waktu penyelesaian, agent tercepat, agent terlambat.
- Statistik agent berdasarkan jumlah tiket.
- Grid agent: kartu agent dengan ringkasan tugas dan metrik.
- Popup detail agent untuk melihat breakdown lebih lengkap.
- Tombol download PDF (menampilkan overlay "Menyiapkan PDF…").

Cara pakai:
1. Buka `Agent Report`.
2. Lihat ringkasan AHT untuk insight cepat.
3. Klik agent untuk membuka popup detail (riwayat tiket, AHT per agent).
4. Untuk mendownload laporan, klik tombol `Download` — overlay akan muncul dan proses menyiapkan PDF berjalan di background.

Tips:
- Jika overlay download tidak hilang otomatis, klik tombol `Sembunyikan` pada dialog atau refresh.
- Jika data kosong, periksa filter date-range dan sumber data di backend.

Troubleshooting:
- Jika tombol download tidak merespon, cek console JS untuk error, dan minta dev periksa endpoint generasi PDF.