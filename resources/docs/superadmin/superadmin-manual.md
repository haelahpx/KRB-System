# Manual Superadmin — KRBS System

## Siapa Superadmin?
Superadmin punya akses paling luas: mengelola akun admin, departemen, ruangan, kendaraan, dokumen/paket, dan fitur konfigurasi tingkat tinggi. Gunakan hak ini hati‑hati dan dokumentasikan setiap perubahan.

---

## Ringkasan Tugas Utama
- Memantau Dasbor (KPI, antrian tiket, SLA, trend aktivitas).
- Mengelola user/admin/department dan peran.
- Menangani eskalasi tiket atau menetapkan agent senior.
- Mengelola master data: ruangan, kendaraan, storage, paket, wifi.
- Review dan generate laporan (PDF/Excel) untuk manajemen.

---

## Dashboard Superadmin
- Fungsi: ringkasan KPI, statistik bulanan, snapshot SLA, dan grafik aktivitas.
- Cara pakai:
  - Buka menu `Dashboard` setelah login Superadmin.
  - Lihat KPI utama (kpi cards), SLA snapshot, dan chart bulanan.
  - Klik item chart atau KPI untuk buka modul terkait.
- Troubleshoot: jika chart kosong, refresh; bila data salah, periksa sumber API atau filter tanggal.

---

## Ticket / Ticket Support
- Modul ini untuk memantau dan menindak tiket support dari semua departemen.
- Fitur penting:
  - Filter by status/prioritas/department/agent.
  - Tampilan kartu tiket (subject, pemohon, priority, status).
  - Modal form untuk buat/ubah tiket (di mobile ada opsi filter overlay).
- Alur tindakan:
  1. Gunakan filter untuk temukan tiket prioritas.
  2. Buka detail tiket untuk melihat lampiran, komentar, dan assignment.
  3. Assign ke agent atau handle langsung; tambahkan komentar diskusi.
  4. Ubah status sesuai progress (Open → In Progress → Resolved → Closed).
- Tips:
  - Cek lampiran sebelum menutup tiket.
  - Gunakan priority `High/Urgent` untuk insiden yang mempengaruhi banyak user.

---

## Ticket Detail
- Halaman detail menampilkan informasi lengkap: subject, description, attachments, comments, dan actions.
- Lihat preview gambar di modal; file non‑gambar bisa diunduh.
- Bagian Discussion: pakai untuk komunikasi antar admin/agent; komentar bersifat kronologis.

---

## Room Booking & Room Monitoring (Superadmin)
- Modul `Booking Room` & `Room Monitoring` untuk memantau semua booking (offline dari resepsionis dan online dari user).
- Fitur:
  - Filter berdasarkan tanggal/ruangan/departemen/status.
  - Grid terpisah untuk offline dan online booking.
  - Detail modal menampilkan requirement, catatan, dan opsi approve/reject/delete.
- Langkah cepat approve:
  1. Buka `Room Monitoring` → saring dengan status `PENDING`.
  2. Klik booking, cek requirement dan konflik jadwal pada calendar.
  3. Approve atau Reject dengan alasan.
- Hati‑hati: sebelum approve, pastikan tidak ada konflik dan peralatan (AV) tersedia bila diminta.

---

## Booking Room — Detail View
- Halaman `bookroomdetails` menampilkan status, label online/meeting, dan info peserta.
- Modal edit tersedia untuk ubah catatan/reschedule jika diperlukan.

---

## Vehicle Management & Booking Vehicle
- Superadmin dapat menambah kendaraan, melihat status booking, dan meninjau foto bukti kondisi.
- Fitur:
  - Form pemesanan (buat/ubah), upload foto sebelum/sesudah, dan status workflow (pending → approved → on_progress → returned → completed).
  - Halaman detail booking (lihat foto, notes, driver info).
- Proses pengembalian:
  - Minta foto bukti kondisi kendaraan saat dikembalikan.
  - Catat kerusakan bila ada dan buat ticket support jika perlu perbaikan.

---

## Documents / DocPac / Package Management
- Modul untuk mencatat paket/dokumen masuk dan keluar.
- Fitur:
  - Input data paket (pengirim/penerima, nomor resi, deskripsi), upload foto bukti.
  - Status: pending → stored → delivered.
  - History dan pencarian untuk tracing.
- Praktik: minta tanda terima penerima dan simpan foto sebagai bukti.

---

## Guestbook Management
- Mencatat tamu yang datang: nama, tujuan, orang yang dikunjungi, waktu kedatangan.
- Fitur: cetak visitor pass (jika sistem mendukung), tandai saat tamu keluar.
- Gunakan untuk audit keamanan bila ada insiden.

---

## Information (Pengumuman) & WiFi Management
- Information: buat/publish pengumuman internal, upload lampiran.
- Modal create/edit untuk menambahkan konten dan tanggal berlaku.
- WiFi Management: tambah SSID/password/lokasi dan toggle visibility.
- Tips: gunakan nama SSID yang konsisten sertakan lokasi agar mudah dicari.

---

## Master Data: Department, Room, Requirement, Storage
- Department: tambah/edit/hapus department; atur department_name dan link ke user default.
- Room: manajemen ruangan (nama, kapasitas, fasilitas). Perubahan room dapat mempengaruhi booking aktif — koordinasikan perubahan.
- Requirement: daftar kebutuhan ruangan (proyektor, mic) yang dipakai di booking form.
- Storage: lokasi penyimpanan paket/dokumen internal.

---

## Packagemanagement & Documentsmanagement
- Packagemanagement untuk paket internal (paket yang dikirim/diterima oleh perusahaan).
- Documentsmanagement untuk menyimpan data invoice/telepon/dokumen penting.
- Pastikan metadata terisi (nomor resi, tanggal, penerima) untuk kemudahan pelacakan.

---

## Report & KPI (Generate dan Download)
- Modul Report menyediakan KPI snapshot, SLA, charts, dan tabel data.
- Tombol download menyiapkan PDF/Excel — overlay menampilkan progress; bila overlay tidak hilang, klik `Sembunyikan` atau refresh.
- Tips: atur range tanggal & filter sebelum generate untuk hasil relevan.

---

## Storage Management
- Kelola lokasi penyimpanan fisik atau virtual untuk paket/dokumen.
- Gunakan untuk tracking lokasi barang saat disimpan di gudang atau ruang penyimpanan.

---

## Vehicle Details & BookingVehicleDetails
- Halaman detail menampilkan ringkasan booking kendaraan, status, foto bukti, notes, dan tombol aksi (approve/reject/mark returned).
- Untuk klaim kerusakan, simpan foto dan buat ticket pada sistem support.

---

## Tickets Support (Superadmin View)
- Tampilan lebih lengkap untuk Superadmin: akses ke seluruh tiket, opsi assign ke agent, dan statistik.
- Gunakan view ini untuk oversight dan eskalasi ketika agent tidak menyelesaikan masalah pada SLA.

---

## Praktik Kerja & SOP Singkat
- Audit trail: selalu isi catatan singkat saat approve/reject/delete.
- Komunikasi: jika menolak permintaan, tulis alasan yang jelas dan solusinya bila memungkinkan.
- Foto bukti: wajib untuk kendaraan/paket saat status berubah ke returned/delivered.
- Backup data: minta tim infra melakukan backup rutin untuk tabel master dan log.

---

## Troubleshooting Umum
- Chart kosong atau data tidak sinkron: refresh halaman. Jika masih, cek console browser untuk error JS atau hubungi dev.
- Overlay download menempel: klik `Sembunyikan` atau refresh; laporkan jika terus terjadi.
- Modal tidak muncul: bisa karena `wire:key` duplikat setelah Livewire update — refresh biasanya memperbaiki.

---