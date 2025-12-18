# Ticket Admin — Panduan Lengkap Singkat

Tujuan: kelola tiket dukungan, assign agent, komentar internal, dan tutup tiket.

Fitur utama:
- Filter tiket: berdasarkan status, prioritas, assignment, dan pencarian.
- Kartu tiket: ringkasan subject, pemohon, prioritas, dan status.
- Halaman detail tiket: lihat deskripsi, lampiran, komentar, dan tombol aksi (assign, change status, save).

Langkah dasar:
1. Buka `Ticket` dari sidebar.
2. Gunakan filter untuk menemukan tiket (status/prioritas/assignment).
3. Klik kartu tiket untuk buka `Ticket Details`.
4. Di detail: tambahkan komentar, ubah status (Open → In Progress → Resolved → Closed), atau assign ke agent.
5. Setelah selesai, klik `Save` atau `Resolve` sesuai alur organisasi.

Attachment & Preview:
- Admin dapat melihat preview gambar langsung; file non-gambar dapat didownload.

Komentar dan Diskusi:
- Gunakan fitur Discussion untuk komunikasi antar-admin/agent. Komentar muncul kronologis.
- Untuk catatan internal, tandai atau gunakan format yang jelas.

Tips & kebijakan:
- Gunakan priority untuk memprioritaskan penanganan (High untuk insiden kritis).
- Jangan menutup tiket tanpa konfirmasi jika ada bukti lampiran yang belum diperiksa.

Troubleshooting:
- Jika polling terlalu sering menyebabkan lag, periksa pengaturan `wire:poll` atau minta tim infra meninjau performa.