# Dashboard Admin — Panduan Singkat

Fungsi: Dasbor memberi ringkasan realtime tentang tiket, penggunaan ruangan, informasi, dan performa agent.

Apa yang terlihat:
- Kartu statistik: total tiket, total booking ruangan, entri informasi, top agent.
- Grafik aktivitas mingguan: tickets, room bookings, dan information.
- Distribusi prioritas tiket.

Cara pakai:
1. Buka menu `Dashboard` setelah login sebagai admin.
2. Lihat kartu statistik untuk prioritas cepat.
3. Periksa grafik aktivitas untuk tren mingguan.
4. Klik bagian chart atau kartu untuk membuka modul terkait (mis. klik "Tickets" untuk ke modul ticket).

Catatan teknis:
- Halaman melakukan `wire:poll` sehingga data diperbarui otomatis.
- Grafik menggunakan Chart.js; jika grafik kosong, cek apakah sumber data (`weeklyActivityData`) tersedia.

Troubleshooting:
- Jika chart tidak muncul: refresh halaman, dan pastikan tidak ada error di console (masalah parsing JSON pada atribut data-root). Jika masih bermasalah, minta developer cek endpoint data.