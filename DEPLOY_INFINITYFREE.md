# Deploy ScreenTime ke InfinityFree

## Kenapa InfinityFree?
- **Gratis selamanya**, no CC
- **PHP 8.3** + **MySQL** (Laravel 10 compatible)
- **Free SSL** (Let's Encrypt)
- **No ads** di website kamu
- Paling banyak dokumentasi & tutorial untuk Laravel

---

## Langkah 1: Daftar InfinityFree

1. Buka https://www.infinityfree.com
2. Klik **Sign Up** → daftar pake email
3. Verifikasi email
4. Login ke https://panel.infinityfree.com

---

## Langkah 2: Buat Hosting Account

1. Di dashboard, klik **Create Account**
2. Pilih salah satu:
   - **Free subdomain**: pilih `*.epizy.com` atau `*.infinityfree.com`
   - **Custom domain**: kalo punya domain sendiri
3. Klik **Create Account**
4. **Catat** ini:
   - Username (contoh: `if0_12345678`)
   - Password
   - Domain kamu

---

## Langkah 3: Buat Database

1. Di dashboard, klik **MySQL Databases**
2. Buat database baru:
   - **Database name**: `if0_12345678_screentime`
   - **Username**: `if0_12345678_admin`
   - **Password**: bikin sendiri
3. **Catat** semua credentials:
   - DB Host: `sql123.infinityfree.com` (contoh, lihat di dashboard)
   - DB Name: `if0_12345678_screentime`
   - DB Username: `if0_12345678_admin`
   - DB Password: password yang kamu buat

---

## Langkah 4: Upload File

### Via File Manager (di dashboard):
1. Klik **File Manager** → buka folder `htdocs/`
2. Upload **SELURUH project** ScreenTime ke `htdocs/`

### Atau via FTP (lebih gampang kalo file banyak):
1. Download FileZilla
2. Connect pake credentials dari dashboard:
   - Host: `ftpupload.net`
   - Username: `if0_12345678`
   - Password: password kamu
   - Port: `21`
3. Upload semua file ke folder `htdocs/`

### Yang di-upload:
```
htdocs/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── vendor/          ← INI WAJIB! (isi 9000+ file)
├── .env
├── artisan
├── composer.json
└── composer.lock
```

> **PENTING:** Folder `vendor/` WAJIB di-upload! Ini berisi semua dependencies Laravel. Upload via FTP lebih stabil.

---

## Langkah 5: Buat .htaccess di htdocs/

Buat file `.htaccess` di folder `htdocs/` (bukan di `public/`):

```apache
RewriteEngine On
RewriteRule ^storage/(.*)$ /storage/app/public/$1 [END]
RewriteRule (.*) /public/$1 [L]
```

> File ini redirect semua request ke folder `public/` di dalam Laravel.

---

## Langkah 6: Buat File .env

Edit file `.env` di `htdocs/.env`:

```env
APP_NAME=ScreenTime
APP_ENV=production
APP_KEY=base64:LTKbIgbHCiDAtsUxpz7GRxsxUvFlV7B2MwAwIOd+RAc=
APP_DEBUG=false
APP_URL=https://yourdomain.epizy.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sql123.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_12345678_screentime
DB_USERNAME=if0_12345678_admin
DB_PASSWORD=password_mysql_kamu

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

> **Ganti** semua value `if0_12345678`, `sql123`, `yourdomain`, dan `password_mysql_kamu` dengan yang benar dari dashboard InfinityFree!

---

## Langkah 7: Setup Database

### Cara 1: Import SQL (Recommended)

Di lokal, jalankan:
```bash
php artisan schema:dump --no-interaction
```

Ini bikin file `database/schema/mysql-schema.sql`.

Upload file ini, terus import via **phpMyAdmin** di dashboard InfinityFree.

### Cara 2: Pakai Route Setup

Buka browser, akses:
```
https://yourdomain.epizy.com/setup/screentime-setup-2026
```

---

## Langkah 8: Set Permissions

Di File Manager:
1. Klik kanan folder `storage/` → **Change Permissions**
2. Set ke **775** (centang Recursive)
3. Lakukan hal sama untuk `bootstrap/cache/`

---

## Langkah 9: Test Website

Buka: `https://yourdomain.epizy.com`

---

## Troubleshooting

### "500 Internal Server Error"
- Cek `.env` (APP_KEY, DB credentials)
- Pastikan `storage/` writable (775)

### "Class not found"
- Pastikan folder `vendor/` ter-upload lengkap (9000+ file)

### CSS/JS rusak
- Pastikan folder `build/` ada di `public/build/`

### "SQLSTATE Connection Error"
- Pastikan DB Host bukan `localhost` (lihat di dashboard)
- Pastikan nama DB benar (ada prefix `if0_`)

### Halaman kosong
- Cek `APP_DEBUG=true` di `.env` buat liat error

---

## Setelah Deploy Berhasil

1. Hapus route `/setup/{key}` dari `routes/web.php`
2. Set `APP_DEBUG=false` di `.env`
3. Commit & push perubahan

---

## URL Final
```
https://yourdomain.epizy.com
```

## Limitations (Free Hosting)
- **No SSH/Composer** di server → upload vendor dari lokal
- **No cron jobs** → queue sync
- **50MB MySQL** limit → cukup buat portfolio
- **30,000 hits/day** → cukup buat demo

---

## Tips
- Jangan upload `node_modules/` (cuma upload `vendor/`)
- `vendor/` = 9000+ file, upload via FTP lebih stabil
- Kalo error `vendor/` tidak lengkap, upload ulang via FTP
