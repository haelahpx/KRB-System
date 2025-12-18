# Manual Pengguna — Sistem Pemesanan Kendaraan

## Ringkasan Singkat
- Sistem ini untuk memesan kendaraan dinas/operasional.
- Kamu bisa pesan untuk sehari, 24 jam, atau waktu kustom.
- Untuk pengemudi wajib punya SIM A — sistem meminta konfirmasi.
- Saat booking selesai/approved, sistem bisa meminta foto "sebelum" dan/atau "sesudah" sebagai bukti.

---

## Aturan & Ketentuan Penting
- Pastikan kamu punya SIM A jika pakai kendaraan perusahaan.
- Setujuin syarat & ketentuan sebelum mengirim permintaan.
- Pilih mode pemesanan: Seharian (08:00-17:00), 24 Jam, atau Kustom (isi tanggal & jam sendiri).
- Jika memilih kendaraan tertentu tapi saat itu tidak tersedia, pilih kendaraan lain atau biarkan sistem memilih otomatis.
- Ikuti kebijakan area ganjil/genap jika berkaitan.

---

## Sebelum Membuat Permintaan
1. Siapkan nama pemohon dan departemen.
2. Tentukan durasi: seharian, 24 jam, atau kustom.
3. Tentukan tanggal mulai dan selesai (untuk kustom juga isi jam mulai & selesai jika diminta).
4. Catat tujuan, jenis keperluan (dinas, operasional, antar jemput, dll), dan apakah masuk area ganjil/genap.
5. Siapkan SIM A dan konfirmasi kamu memilikinya.

---

## Cara Memesan Kendaraan (Langkah demi langkah)
1. Buka menu "Pesan Kendaraan".
2. Isi nama dan departemen (nama biasanya otomatis jika sudah login).
3. Pilih durasi pemesanan:
   - Seharian: default jam 08:00–17:00.
   - 24 Jam: +1 hari.
   - Kustom: isi tanggal & jam mulai/selesai sendiri.
4. Isi tanggal mulai dan selesai.
5. Jika mode bukan kustom, bagian tanggal/waktu mungkin terkunci (readonly).
6. Isikan tujuan, jenis keperluan, dan area ganjil/genap jika ada.
7. (Opsional) Pilih kendaraan tertentu jika tersedia, jika tidak biarkan kosong.
8. Centang konfirmasi punya SIM A dan setuju syarat & ketentuan.
9. Tekan “Kirim Permintaan”.

Catatan: Jika kendaraan yang dipilih tidak tersedia pada tanggal/waktu yang diminta, opsi akan dinonaktifkan dan sistem meminta pilih kendaraan lain.

---

## Setelah Mengirim: Apa yang Terjadi
- Tiket pemesanan masuk ke antrian dengan status `pending` (tertunda).
- Admin/penanggung jawab akan meninjau dan mengubah status menjadi `approved` (disetujui), `rejected` (ditolak), `on_progress` (sedang berlangsung), `returned` (dikembalikan), `cancelled`, atau `completed`.
- Jika disetujui dan membutuhkan bukti, sistem akan meminta kamu unggah foto sebelum berangkat (check-out) atau setelah selesai (check-in), tergantung alur.

---

## Unggah Foto (Check-Out / Check-In)
- Ketika pemesanan berstatus `approved` atau `returned`, halaman pemesanan akan menyediakan mode unggah foto.
- Fungsinya:
  - Foto sebelum berangkat: dokumentasi kondisi kendaraan sebelum dipakai.
  - Foto sesudah (setelah kembali): dokumentasi kondisi setelah pemakaian.
- Cara unggah:
  1. Klik area "Tambah foto" lalu pilih file dari perangkat.
  2. Bisa pilih banyak foto sekaligus.
  3. Jika pakai laptop, ada opsi webcam untuk ambil foto langsung.
  4. Setelah memilih foto, akan muncul preview; kamu bisa hapus foto sebelum mengunggah.
  5. Tekan tombol "Unggah X Foto" untuk mengirim ke server.
- Pastikan foto jelas, menampilkan bagian penting seperti nomor polisi, goresan, atau kondisi yang relevan.

---

## Melihat Status Pemesanan (Halaman Status Kendaraan)
- Buka menu "Status Kendaraan".
- Gunakan kolom pencarian untuk cari berdasarkan tujuan atau nama kendaraan.
- Gunakan filter untuk memilih kendaraan tertentu, status, atau urutan (terbaru/terdekat).
- Di daftar hasil kamu akan lihat ringkasan pemesanan: tujuan, tanggal, jam, kendaraan, dan badge status.

Status yang umum:
- `pending` — permintaan baru, menunggu persetujuan.
- `approved` — disetujui; jika perlu, siap untuk upload foto sebelum/selesai.
- `on_progress` — sedang digunakan.
- `returned` — kendaraan sudah dikembalikan (mungkin menunggu verifikasi foto).
- `rejected` — permintaan ditolak (ada catatan/penjelasan).
- `cancelled` — dibatalkan.
- `completed` — proses selesai.

---

## Detail Permesanan & Catatan
- Setiap pemesanan menampilkan bagian detail seperti peminjam, tujuan, jenis, area ganjil/genap, catatan/penolakan, jumlah foto sebelum/sesudah, dan tanggal pembuatan/pembaruan.
- Jika ada penolakan, alasan akan ditampilkan di blok merah.
- Jika ada catatan, tampil di blok abu-abu.

---

## Batal & Perubahan
- Jika ingin membatalkan sebelum disetujui, gunakan tombol batal (jika tersedia) atau hubungi admin.
- Jika perlu perubahan tanggal/waktu setelah disetujui, hubungi admin atau buat permintaan baru jika tidak memungkinkan diubah.

---

## Tips Praktis
- Pilih kendaraan cadangan jika kendaraan pilihanmu tidak tersedia.
- Unggah foto jelas dan lengkap untuk menghindari klaim kerusakan yang tidak perlu.
- Jika ada area ganjil/genap, cantumkan info agar pengemudi menyiapkan dokumen tambahan jika perlu.
- Tuliskan tujuan singkat tapi jelas supaya admin bisa menilai kebutuhan (mis. "Antar Barang ke Cabang A").

---

## Troubleshooting Umum
- Upload foto gagal: cek koneksi internet; coba kompres gambar jika besar.
- Kamera tidak aktif: periksa izin kamera di browser/HP.
- Tidak bisa pilih tanggal/waktu: pastikan mode booking sesuai (kustom untuk jam manual).
- Kendaraan tidak muncul: artinya kendaraan sedang tidak tersedia untuk waktu yang dipilih.

---

## Contoh Pengisian yang Baik
- Nama: Budi Santoso
- Departemen: Operasional
- Mode: Seharian
- Tanggal: 20 Desember 2025
- Tujuan: Kirim dokumen ke Cabang Bandung
- Jenis: Operasional (Logistik)
- Area ganjil/genap: Tidak
- Pilih kendaraan: Biarkan kosong jika tidak penting
- Centang: Saya memiliki SIM A; Setuju Syarat & Ketentuan

---

## Peran Admin / Agen (Singkat)
- Tinjau permintaan dan setujui atau tolak.
- Minta foto bukti jika diperlukan.
- Tandai status sesuai alur (approved, on_progress, returned, completed).
- Beri alasan saat menolak agar pemohon paham kenapa.

---

## FAQ
Q: Apa itu mode "Seharian" vs "24 Jam"?
A: "Seharian" biasanya jam kerja standar (mis. 08:00–17:00). "24 Jam" berarti kendaraan dipakai selama 24 jam (berlaku untuk kebutuhan khusus).

Q: Apakah wajib pilih kendaraan?
A: Tidak wajib — kamu bisa biarkan kosong agar sistem memilih kendaraan yang tersedia.

Q: Kenapa diminta unggah foto?
A: Untuk bukti kondisi kendaraan sebelum dan sesudah penggunaan sebagai dokumentasi dan pengamanan.

Q: Apa yang terjadi kalau tidak punya SIM A?
A: Sistem meminta konfirmasi; tanpa SIM A biasanya permintaan tidak akan disetujui.

---

## Kontak & Jam Operasional
- Jam operasional layanan: Senin–Jumat 08:00–17:00; Sabtu 08:00–13:00; Minggu tutup.
- Untuk masalah darurat, hubungi kontak darurat internal yang biasa digunakan organisasi.

---