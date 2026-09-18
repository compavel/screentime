# Deploy ScreenTime ke 000webhost

## Yang Diperlukan
- Akun GitHub (sudah punya: `compavel`)
- Akun 000webhost (gratis, daftar di 000webhost.com)

---

## Langkah 1: Buat Akun & Database di 000webhost

1. Daftar di https://www.000webhost.com
2. Buat website baru (pilih PHP, kosongkan dulu)
3. Buka **Dashboard** → **Database**
4. Buat database baru:
   - **Database name:** `idXXXXXXXX_screentime` (otomatis ada prefix `idXXXXXXXX_`)
   - **Username:** `idXXXXXXXX_admin`
   - **Password:** bikin sendiri (catat!)
5. **Catat** nama database, username, dan password

---

## Langkah 2: Siapkan File di 000webhost

Buka **File Manager** di dashboard 000webhost.

### Struktur folder yang benar:
```
/home/aXXXXXXX/
├── app/                    ← pindahkan dari public_html
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/                ← pastikan writable
├── vendor/
├── .env
├── .env.000webhost
├── artisan
├── composer.json
├── composer.lock
└── public_html/            ← document root (isi dari folder public/)
    ├── index.php
    ├── .htaccess
    └── build/
```

### Cara upload:
1. **Upload semua file** ScreenTime ke folder `public_html/` (kecuali folder `build/` jika sudah ada)
2. **Pindahkan** folder `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`, `vendor/` KE ATAS `public_html/` (satu level di atas)
3. **Pindahkan** `.env`, `.env.000webhost`, `artisan`, `composer.json`, `composer.lock` juga ke atas
4. Yang **TINGGAL di public_html/**: hanya `index.php`, `.htaccess`, dan folder `build/`

---

## Langkah 3: Buat File .env

Di File Manager, buat file `.env` di **root** (satu level di atas `public_html/`):

```env
APP_NAME=ScreenTime
APP_ENV=production
APP_KEY=base64:LTKbIgbHCiDAtsUxpz7GRxsxUvFlV7B2MwAwIOd+RAc=
APP_DEBUG=false
APP_URL=https://namawebsite.000webhostapp.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=idXXXXXXXX_screentime
DB_USERNAME=idXXXXXXXX_admin
DB_PASSWORD=password_kamu_di_sini

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

> **Ganti** `idXXXXXXXX`, `namawebsite`, dan `password_kamu_di_sini` dengan yang benar!

---

## Langkah 4: Fix Permissions

Di File Manager, klik kanan folder `storage/` → **Change Permissions**:
- Centang **Recursive** (_apply ke semua subfolder_)
- Set **775** atau **777**

Lakukan hal yang sama untuk folder `bootstrap/cache/`.

---

## Langkah 5: Run Setup (Migration)

Buka browser, akses:

```
https://namawebsite.000webhostapp.com/setup/screentime-setup-2026
```

Jika berhasil, akan muncul:
```
Setup Berhasil!

Migrating: xxxxxxx_xxx_create_users_table
Migrated: xxxxxxx_xxx_create_users_table
...
```

> **PENTING:** Setelah setup berhasil, **HAPUS route setup** dari `routes/web.php` untuk keamanan!

---

## Langkah 6: Test Website

Buka:
```
https://namawebsite.000webhostapp.com
```

---

## Troubleshooting

### "500 Internal Server Error"
- Cek file `.env` sudah benar (APP_KEY, DB credentials)
- Pastikan `storage/` dan `bootstrap/cache/` writable (775/777)

### "Class not found"
- Pastikan folder `vendor/` ter-upload lengkap

### Halaman blank / CSS rusak
- Pastikan folder `build/` ada di `public_html/build/`

### "SQLSTATE[HY000] Connection refused"
- Pastikan `DB_HOST=localhost` (bukan 127.0.0.1)
- Pastikan nama database benar (termasuk prefix `idXXXXXXXX_`)

---

## Setelah Deploy

1. **Hapus route setup** dari `routes/web.php` (baris 15-37)
2. **Commit & push** perubahan
3. **Re-deploy** jika menggunakan GitHub auto-deploy

---

## URL Final
```
https://namawebsite.000webhostapp.com
```

## Default Account
Buat akun baru langsung di website (Register).
