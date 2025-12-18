# Room Monitoring / Booking Admin — Panduan

Fungsi: memantau booking ruangan (offline & online), meninjau detail, dan mengelola riwayat.

Area dalam modul:
- Filter & Search: cari berdasarkan status, ruangan, tanggal, atau pemohon.
- Grid offline/online: daftar booking terpisah untuk yang dibuat lewat resepsionis (offline) dan user (online).
- Detail Modal: buka untuk melihat requirement, catatan, dan opsi aksi (approve/reject/delete).
- Delete Confirmation Modal untuk menghapus booking jika perlu.

Cara meninjau & mengubah:
1. Buka `Room Monitoring`.
2. Gunakan filter untuk menyaring booking (mis. status = PENDING).
3. Klik booking untuk buka modal detail.
4. Di modal: periksa requirement, tambahkan catatan, lalu approve atau reject. Jika delete, gunakan konfirmasi delete.

Best practices:
- Periksa konflik jadwal sebelum approve.
- Simpan alasan penolakan secara singkat tapi jelas.

Troubleshooting:
- Jika detail modal tidak muncul, cek apakah `wire:key` untuk modal unik; reload halaman biasanya mengatasi masalah UI sementara.