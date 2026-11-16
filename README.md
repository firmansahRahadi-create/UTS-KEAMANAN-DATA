# Sistem Kriptografi Hybrid

Aplikasi PHP sederhana untuk menyimpan konten admin dengan enkripsi AES-256-CBC dan RSA-2048.

## Persyaratan

- PHP dengan ekstensi `mysqli` dan `openssl`
- MySQL

## Menjalankan di Laragon

1. Letakkan folder proyek di direktori web Laragon.
2. Atur kredensial MySQL di `koneksi.php` sesuai konfigurasi lokal. Secara default, aplikasi memakai `localhost`, user `root`, tanpa password, dan membuat database `db_keamanan_data`.
3. Atur `APP_ADMIN_USERNAME` dan `APP_ADMIN_PASSWORD` pada environment yang digunakan Apache/PHP, lalu restart Laragon. Opsional: atur `APP_ADMIN_NAME`.
4. Buka `http://localhost/kriptografi-hybrid/login.php`.

Akun admin hanya dibuat otomatis jika kedua variabel `APP_ADMIN_USERNAME` dan `APP_ADMIN_PASSWORD` tersedia. Gunakan password yang kuat dan jangan menyimpan kredensial di source code atau repository.
