# Manual Resepsionis — KRBS System

**Catatan singkat**: Simpan link ini untuk rujukan cepat. Jika perlu format lain (PDF), sebutkan di akhir.

---

## 1. Home (Dasbor Receptionist)
Tujuan: tampilan ringkasan realtime — antrian tiket, statistik, status sistem, pengumuman, dan akses cepat.

Apa yang ada:
- Panel agen: jumlah antrian per departemen, tugas aktif, dan tombol `Ke Workspace` untuk masuk ke area pengelolaan tiket.
- Ringkasan pengguna: jika bukan agen, tampil jumlah tiket pribadi dan tombol `Buat Baru` / `Lihat Semua`.
- Wifi carousel: SSID/password dan tombol salin.
- System health: status Server, Database, Network (indikator warna hijau/merah).
- Pengumuman & Informasi: daftar pengumuman dan update operasional.

Cara pakai:
- Lihat panel agen untuk prioritas tiket. Klik `Ke Workspace` untuk menangani antrian.
- Gunakan tombol `Laporkan Bug` atau `Dukungan IT` untuk insiden teknis (mailto link sudah tersedia).
- Periksa badge `LIVE` dan angka antrian untuk prioritas tindakan.

Tips & troubleshooting:
- Jika angka antrian tidak update, refresh browser. Sistem juga melakukan polling otomatis.
- Jika wifi tidak bisa disalin, periksa izin clipboard di browser.

---

## 2. Room Management
Group di sidebar: Booking Room, Booking Approval, Booking History.

A. Booking Room (Tambah / Kelola Pemesanan)
- Fungsi: buat pemesanan ruangan offline (resepsionis bisa input atas nama user) dan/atau pengelolaan form online.
- Field penting: pemohon, departemen, ruangan, tanggal, jam mulai/selesai, kebutuhan tambahan (proyektor, lain-lain), catatan khusus, dan opsi informasikan Departemen Informasi.
- Mode: online form (user) dan offline form (resepsionis bisa menyimpan sebagai pending).

Langkah cepat (resepsionis membuat booking offline):
1. Buka `Booking Room` → pilih "Tambah Booking Ruangan (Offline)".
2. Isi nama pemohon atau cari dari daftar; pilih departemen.
3. Pilih ruangan, tanggal, dan jam. Jika ada pilihan `Other` di requirements, tulis keterangan di catatan.
4. Centang `Inform Information Dept` bila perlu bantuan teknis.
5. Simpan; booking akan masuk dengan status `Tertunda`.

B. Booking Approval (Persetujuan)
- Fungsi: daftar permintaan yang menunggu resepsionis (pending) serta daftar rapat yang sedang berlangsung (ongoing).
- Tindakan: Approve, Reject, jadwalkan ulang, atau tambahkan catatan/penolakan.

Cara meninjau:
- Buka `Booking Approval`. Daftar `Menunggu Persetujuan` muncul di atas.
- Klik item untuk melihat detail (judul, pemohon, waktu, kebutuhan tambahan, catatan).
- Jika approve: ubah status, tambahkan catatan jika perlu; jika reject: beri alasan (alasan ditampilkan ke pemohon).

Tips:
- Periksa konflik jadwal di calendar sebelum approve.
- Jika rapat sedang berlangsung, pantau status ongoing dan catat bila diperlukan.

C. Booking History (Riwayat)
- Fungsi: riwayat semua booking, pencarian, dan filter berdasarkan tanggal/ruangan/status.
- Gunakan untuk audit, melihat penggunaan ruangan, dan menindaklanjuti klaim.

---

## 3. Vehicle Management
Group: Book Vehicle, Vehicle Status, Vehicle History.

A. Book Vehicle (Form pemesanan & unggah foto)
- Resepsionis dapat membuat pemesanan atas nama user atau mengelola unggahan foto check-out/check-in ketika booking sudah approved.
- Mode pemesanan: Seharian, 24 Jam, atau Kustom.
- Field penting: nama pemohon, departemen, tanggal mulai/selesai, waktu, tujuan, jenis keperluan, area ganjil/genap, pilihan kendaraan (opsional), konfirmasi SIM A, dan persetujuan syarat.

Langkah (membuat booking):
1. Buka `Book Vehicle` → isi form sesuai data pemohon.
2. Pilih mode durasi; jika `Kustom`, pastikan jam mulai/selesai diisi.
3. Centang konfirmasi SIM A jika peminjam memiliki.
4. Simpan; status awal biasanya `pending`.

Unggah foto (setelah approved):
- Jika status `approved` atau `returned`, sistem meminta foto sebelum/perjalanan (sebelum) atau setelah (sesudah) untuk dokumentasi kondisi kendaraan.
- Cara unggah: pilih file, lihat preview, hapus bila perlu, lalu klik `Unggah X Foto`.

B. Vehicle Status
- Fungsi: daftar semua pemesanan kendaraan dengan filter (kendaraan, status, sort). Menampilkan badge status (pending, approved, on_progress, returned, rejected, completed, cancelled).
- Untuk setiap item: lihat jumlah foto sebelum/sesudah, notes, dan link ke halaman unggah jika perlu.

Tindakan resepsionis:
- Setujui, tolak, atau minta dokumen tambahan.
- Minta pengemudi/peminjam unggah foto bukti jika ada klaim kerusakan.

C. Vehicle History
- Riwayat pemakaian kendaraan, berguna untuk audit, klaim, dan laporan operasional.
- Gunakan filter tanggal, kendaraan, dan status untuk menemukan catatan.

---

## 4. Guest Management
Group: GuestBook, GuestBook History.

A. GuestBook (Daftar tamu masuk / pengisian buku tamu)
- Fungsi: input data tamu yang datang (nama, perusahaan, tanggal, jam, orang yang dikunjungi, keperluan, kontak).
- Resepsionis mengisi data tamu, mencetak visitor pass jika perlu, dan menandai saat tamu keluar.

Langkah singkat:
1. Buka `GuestBook` → klik Tambah/Isi form tamu.
2. Isi data wajib: nama, telepon/email (jika ada), tujuan kunjungan.
3. Simpan; sistem menyimpan timestamp kedatangan.

B. GuestBook History
- Riwayat tamu yang pernah datang; bisa dicari berdasarkan nama, tanggal, atau tujuannya.
- Gunakan untuk keperluan keamanan, audit, atau follow-up.

Tips keamanan:
- Minta identitas jika prosedur organisasi mengharuskan.
- Simpan catatan foto atau dokumen jika tamu membawa barang berharga.

---

## 5. DocPac Management (DocPac Form, DocPac Status, DocPac History)
DocPac = pengelolaan pengiriman paket/dokumen internal.

A. DocPac Form
- Fungsi: input paket/dokumen masuk/keluar, arah (in/out), tipe (document/package), penyimpanan, dan bukti foto.
- Resepsionis mengisi detail paket, penerima, tanggal, dan bisa mengambil foto bukti lewat webcam atau upload file.

Langkah cepat:
1. Buka `DocPac Form` → pilih direction (in/out), type (document/package), storage lokasi.
2. Isi pengirim/penerima, nomor resi jika ada, dan deskripsi isi paket.
3. Unggah foto bukti (opsional tapi direkomendasikan) lalu simpan.

B. DocPac Status
- Memantau paket yang sedang diproses: pending, stored, delivered, dll.
- Tampilkan badge status, tanggal, dan catatan penanganan.

Resepsionis bisa:
- Mengubah status paket saat dipindahkan ke storage atau diambil.
- Menambahkan catatan untuk penerima.

C. DocPac History
- Riwayat semua paket/dokumen, lengkap dengan filter pencarian.
- Gunakan untuk tracing paket, audit, dan laporan operasional.

---

## 6. Umum: Praktik Kerja dan Tips Resepsionis
- Selalu periksa slot/dates sebelum approve booking untuk menghindari konflik.
- Tambahkan catatan rinci saat menolak permintaan (supaya pemohon paham alasannya).
- Untuk pengembalian kendaraan/paket, minta foto bukti kondisi untuk menghindari klaim.
- Gunakan polling/refresh otomatis tetapi jika UI terlihat kacau, refresh manual browser.
- Saat menerima panggilan darurat/insiden, segera gunakan tombol `Laporkan Bug` atau kontak email support.

---

## 7. Troubleshooting Cepat & FAQ
Q: Bagaimana bila ada konflik jadwal setelah approve?
- A: Segera komunikasikan ke pemohon yang terdampak dan tawarkan opsi alternatif; ubah jadwal jika memungkinkan.

Q: Foto tidak terunggah atau error kamera?
- A: Periksa izin kamera/browser, ukuran file, atau coba unggah dari perangkat lain.

Q: Bagaimana menolak booking dengan alasan yang baik?
- A: Tulis alasan singkat dan jelas (mis. "Ruangan sudah terpakai pada jam tersebut" atau "Kapasitas ruangan kurang").

---