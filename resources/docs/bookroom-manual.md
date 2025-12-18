# Manual Pengguna — Pemesanan Ruangan (Luring)

## Ringkasan Singkat
- Untuk memesan ruang pertemuan fisik (luring) di sistem ini.
- Bisa pakai Form penuh atau Calendar (klik slot) untuk pemesanan cepat.
- Slot tiap 30 menit; pemesanan minimal mulai 15 menit dari sekarang.

---

## Aturan Utama
- Durasi pemesanan menggunakan kelipatan slot 30 menit.
- Jangan pilih jam yang sudah lewat; sistem akan geser otomatis atau menolak.
- Isi judul meeting dengan jelas.
- Jika butuh peralatan (proyektor, whiteboard, dsb), centang di kolom "Kebutuhan Tambahan".

---

## A. Cara Memesan — Form Lengkap
1. Buka halaman "Luring (Ruangan)" → pilih tab **Form**.
2. Isi *Judul Pertemuan* singkat dan jelas.
3. Pilih *Ruangan* dari daftar (hanya yang tersedia untuk kebutuhan Anda akan aktif).
4. Pilih *Tanggal* pertemuan.
5. Isi *Jumlah Peserta* (angka minimal 1).
6. Pilih *Waktu Mulai* dan *Waktu Berakhir*.
   - `start_time` harus ≥ `minStart` (minimal 15 menit dari sekarang).
   - `end_time` minimal sama atau lebih besar dari `start_time`.
7. Centang kebutuhan tambahan jika perlu.
   - Jika pilih **Lainnya**, akan muncul kotak *Catatan Khusus* untuk menuliskan permintaan spesifik.
8. Centang opsi untuk memberitahu Departemen Informasi jika Anda ingin mereka menindaklanjuti (opsional).
9. Klik **Kirim Permintaan** untuk mengajukan pemesanan.

Pesan kesalahan akan muncul di bawah field jika ada input yang tidak valid.

---

## B. Cara Memesan — Kalender (Quick Booking)
- Buka tab **Calendar** untuk melihat jadwal per-slot per-ruangan.
- Navigasi tanggal dengan tombol prev/next, atau input tanggal langsung.
- Geser (mobile) atau scroll (desktop) untuk melihat ruangan lainnya.
- Warna slot:
  - Merah: sudah dipesan
  - Abu-abu: slot sudah lewat / ditutup
  - Putih: tersedia
  - Hijau muda: klik untuk langsung memesan
- Klik slot putih/hijau yang tersedia → muncul *Quick Modal* untuk input cepat (judul, start/end, attendees, requirements).
- Konfirmasi di modal untuk mengirim request cepat.

Catatan: slot yang kurang dari 15 menit dari waktu sekarang dianggap "past" dan tidak bisa dipesan.

---

## C. Kartu Ketersediaan & Pemesanan Terbaru (Sidebar)
- Di sebelah kanan ada kartu yang menampilkan:
  - Daftar ruangan dengan indikator tersedia/terisi.
  - Ringkasan 3 pemesanan terakhir.
- Gunakan ini untuk memilih alternatif waktu atau ruangan.

---

## D. Melihat Status Pemesanan
- Buka halaman **Status Pemesanan Ruangan**.
- Gunakan fitur pencarian dan filter (ruangan, status, urut) untuk menemukan pemesanan.
- Setiap item menampilkan:
  - Judul, ID pemesanan, ruangan, tanggal & jam, badge status (Tertunda / Disetujui / Ditolak / Selesai), dan catatan jika ada.
- Jika ditolak, alasan penolakan ditampilkan di blok merah.

---

## E. Penjelasan Status
- **Tertunda (pending)**: Permintaan baru menunggu persetujuan.
- **Disetujui (approved)**: Ruangan dikonfirmasi.
- **Ditolak (rejected)**: Permintaan ditolak; baca alasan di bagian penolakan.
- **Selesai (completed)**: Pertemuan sudah lewat dan selesai.

---

## F. Tips Praktis
- Cek kalender dulu sebelum memilih waktu agar tidak bolak-balik.
- Isi judul jelas (mis. "Rapat Koordinasi Marketing") supaya reviewer cepat paham.
- Jika butuh alat, centang opsi yang sesuai atau tulis di *Catatan Khusus*.
- Untuk rapat mendesak, jangan pilih jam yang hampir lewat (sistem blok minimal 15 menit ke depan).

---

## G. Troubleshooting Cepat
- Tidak bisa pilih ruangan: kemungkinan ruangan sudah dipesan untuk waktu itu; coba waktu lain.
- Waktu tidak valid: periksa `start_time` dan `end_time`, serta batas minimal 15 menit dari sekarang.
- Catatan tidak muncul: pastikan memilih opsi "Lainnya" di kebutuhan tambahan.

---

## H. FAQ Singkat
Q: Bisa pesan lebih dari 30 menit? — A: Ya, pilih waktu selesai yang mencakup beberapa slot 30 menit.

Q: Bagaimana jika admin menolak? — A: Alasan penolakan ditampilkan; hubungi admin jika butuh klarifikasi.

Q: Dapatkah saya meminta departemen informasi menyiapkan rapat? — A: Centang opsi "Minta Departemen Informasi" di form.

---
