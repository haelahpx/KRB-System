# Manual Admin — KRBS System

**Siapa Admin?**
Admin adalah orang yang mengelola pengguna, konfigurasi utama, dan memantau operasi (dashboard, tiket, room, vehicle, paket, wifi, dll.).
# Manual Admin Lengkap — KRBS System

## Ringkasan peran Admin
- Mengelola user, peran, dan izin.
- Memantau kesehatan sistem lewat Dasbor.
- Menangani dan meng-eskalasi tiket support.
- Mengelola booking ruangan, kendaraan, paket/dokumen, dan informasi internal.

Prinsip: selalu catat alasan perubahan signifikan (approve/reject/delete) agar ada audit trail.

---

## Alur Kerja Singkat
1. Buka `Dashboard` untuk overview: tiket, booking, dan statistik.
2. Tindak tiket prioritas melalui modul `Ticket` (assign, comment, resolve).
3. Kelola user di `User Management` jika perlu ubah peran atau reset password.
4. Tinjau `Room Monitoring` dan `Information` untuk approve booking atau publish pengumuman.
5. Pantau `Agent Report` untuk mengevaluasi performa agent.

---

## Dashboard
Fungsi: ringkasan realtime tentang tiket, penggunaan ruangan, entri informasi, dan performa agent.

Yang perlu diperhatikan:
- Kartu statistik (total tiket, booking, entri informasi, top agent).
- Grafik aktivitas mingguan (tickets, room, information).
- Distribusi prioritas tiket.

Cara pakai:
- Buka menu `Dashboard` setelah login.
- Klik kartu atau elemen chart untuk membuka modul terkait.

Catatan teknis:
- Halaman memakai `wire:poll` jadi data diperbarui otomatis.
- Jika grafik kosong, cek sumber data (`weeklyActivityData`) atau console browser.

---

## Ticket (Admin)
Tujuan: kelola tiket dukungan, assign agent, tambahkan komentar internal, dan tutup tiket.

Fitur utama:
- Filter tiket (status, prioritas, assignment, pencarian).
- Kartu tiket untuk ringkasan cepat.
- Halaman detail tiket (deskripsi, lampiran, komentar, aksi seperti assign/change status/save).

Langkah singkat:
1. Buka `Ticket` dari sidebar.
2. Filter untuk menemukan tiket yang perlu ditindak.
3. Klik tiket untuk buka detail.
4. Di detail: tambahkan komentar, ubah status (Open → In Progress → Resolved → Closed), atau assign ke agent.

Attachment & preview:
- Gambar bisa dipreview langsung; file lain dapat diunduh.

Tips:
- Gunakan priority untuk memprioritaskan penanganan.
- Jangan tutup tiket tanpa memeriksa bukti lampiran.

---

## Room Monitoring / Booking
Fungsi: memantau booking ruangan (offline & online), meninjau detail, dan mengelola riwayat.

Area penting:
- Filter & search untuk menyaring booking.
- Grid offline (resepsionis) dan online (user).
- Detail modal berisi requirement, catatan, dan aksi (approve/reject/delete).

Prosedur singkat:
1. Buka `Room Monitoring`.
2. Saring booking dengan filter (mis. status = PENDING).
3. Klik booking untuk lihat detail dan ambil tindakan (approve/reject).

Best practice:
- Periksa konflik jadwal sebelum approve.
- Tuliskan alasan penolakan secara jelas.

---

## Information (Pengumuman & Request)
Fungsi: membuat, mengedit, memoderasi entri informasi (pengumuman/pesan internal) dan memproses request.

Modul mencakup:
- Request Queue (offline & online), index table, create/edit modal, serta inform/reject modal.

Langkah umum:
1. Buka `Information` di sidebar.
2. Proses item di `Request Queue`: publish, inform, atau reject.
3. Gunakan modal create untuk menambah pengumuman (judul, isi, lampiran, tanggal berlaku).

Catatan:
- Modal bisa ditutup dengan `Esc`.
- Pastikan tanggal berlaku benar sebelum publish.

---

## Agent Report
Fungsi: melihat kinerja agent—Average Handle Time (AHT), jumlah tiket per agent—dan mengunduh laporan.

Yang ada:
- AHT Summary (avg resolution, fastest, slowest agent).
- Statistik agent dan grid agent.
- Popup detail agent dan tombol download PDF (menampilkan overlay "Menyiapkan PDF…").

Cara pakai:
1. Buka `Agent Report`.
2. Klik agent untuk buka detail performa.
3. Klik `Download` untuk mengunduh laporan; jika overlay muncul terus, klik `Sembunyikan` atau refresh.

---

## User Management
Fungsi: tambah, edit, dan hapus user; atur role dan department.

Fitur utama:
- Form tambah user, filters & search, list users, modal edit.

Langkah menambah user:
1. Buka `User Management` → `Tambah User`.
2. Isi nama, email, password, pilih role & department.
3. Simpan.

Keamanan:
- Hindari memberi role `admin`/`superadmin` kecuali perlu.

---

## WiFi Management
Fungsi: mengelola daftar WiFi internal yang tampil untuk karyawan.

Fitur:
- Tambah SSID, password, lokasi, visibility; edit dan delete.

Langkah cepat:
1. Buka `WiFi Management` → `Tambah WiFi Baru`.
2. Isi SSID & password, lokasi, lalu simpan.

---

## Praktik Kerja & Tips
- Selalu catat alasan ketika approve/reject/delete.
- Periksa konflik jadwal sebelum menyetujui booking.
- Minta bukti foto saat pengembalian kendaraan atau paket.
- Jika UI bermasalah, refresh; jika terus bermasalah, laporkan ke `support@krbs.id`.

---

## Troubleshooting Cepat
Q: Grafik dashboard tidak muncul?
- A: Refresh halaman; cek console. Bila error parsing JSON, minta dev cek sumber data.

Q: Tombol download PDF tidak merespon?
- A: Periksa console JS; jika overlay menempel, klik `Sembunyikan` atau refresh.

Q: User tidak dapat login setelah reset password?
- A: Pastikan email aktivasi/konfigurasi mail berfungsi.

---

## Kontak Bantuan
- Dukungan teknis: support@krbs.id
- Eskalasi operasional: kepala resepsionis atau admin operasional sesuai SOP.

---