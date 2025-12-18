# User Management — Panduan Admin

Fungsi: menambah, mengedit, dan menghapus user serta menetapkan peran dan departemen.

Fitur utama:
- Form Tambah User: nama, email, password, role, department, agent flag.
- Filters & Search: cari user berdasarkan nama, role, atau departemen.
- List users dengan aksi cepat: edit, reset password, atau delete.
- Modal Edit: bentuk edit dengan validasi dan tombol simpan.

Langkah menambah user:
1. Buka `User Management` → klik `Tambah User`.
2. Isi data wajib (nama, email, password) dan pilih departemen serta role.
3. Simpan; user baru akan muncul di daftar.

Mengedit & menghapus:
- Klik `Edit` di baris user untuk membuka modal edit.
- Untuk menghapus, pastikan user bukan `admin`/`superadmin` kecuali diinginkan; sistem biasanya mencegah penghapusan role tinggi.

Tips keamanan:
- Jangan berikan akses `admin`/`superadmin` sembarangan.
- Gunakan role granular dan catat perubahan di log jika tersedia.

Troubleshooting:
- Bila user tidak menerima email aktivasi, verifikasi konfigurasi mail di environment atau kirim ulang aktivasi melalui fitur yang ada.