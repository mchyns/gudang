# Setup Install (Fokus Instalasi)

## 1. Install Dependensi Sistem

- PHP `8.3` atau lebih baru
- Composer `2.x`
- Node.js `20+` dan npm
- Database server (MySQL/MariaDB) atau SQLite

## 2. Install Dependensi Project

Jalankan dari root project:

```bash
composer install
npm install
```

## 3. Install Konfigurasi App Dasar

```bash
cp .env.example .env
php artisan key:generate
```

## 4. Install Struktur Database

```bash
php artisan migrate
php artisan db:seed
```

## 5. Install Build Assets Frontend

Untuk build production:

```bash
npm run build
```

Untuk development:

```bash
npm run dev
```

## 6. Opsi Install Sekali Jalan

Alternatif paling cepat:

```bash
composer run setup
```
