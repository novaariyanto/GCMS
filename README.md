# GCMS - Government Complaint Management System

GCMS adalah aplikasi pengelolaan pengaduan masyarakat untuk pemerintah daerah. Sistem ini menyediakan portal masyarakat, API mobile/web berbasis JWT, workflow pengaduan, audit trail, notifikasi, dashboard, dan panel administrasi Filament.

## Stack Teknologi

- PHP 8.4 untuk container, kompatibel aplikasi Laravel 12.
- Laravel 12, Eloquent ORM, Queue, Scheduler, Event, Notification.
- Filament 4 untuk panel admin di `/admin`.
- JWT Auth (`tymon/jwt-auth`) untuk API.
- MySQL 8.4 sebagai database utama.
- Redis untuk cache, session, queue, dan broadcast pendukung.
- Laravel Reverb untuk realtime websocket.
- Pest/PHPUnit untuk automated test.
- Docker Compose untuk lingkungan lokal lengkap.

## Struktur Penting

- `app/Domain` - model domain, enum, event, listener, policy.
- `app/Application` - DTO, service, dan action use case.
- `app/Infrastructure` - repository dan integrasi persistence.
- `app/Http` - controller, request validation, resource API.
- `database/migrations` dan `database/seeders` - schema dan data awal.
- `docs/` - arsitektur, database, diagram, API, dan panduan operasional.
- `docker/` - konfigurasi nginx, PHP, dan supervisor.

## Instalasi Lokal Tanpa Docker

Prasyarat:

- PHP 8.2+ dengan ekstensi `pdo_mysql`, `intl`, `gd`, `zip`, `bcmath`, `redis`, `opcache`.
- Composer 2.
- Node.js 20+ dan npm.
- MySQL 8 atau MariaDB kompatibel.
- Redis.

Langkah:

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
npm run build
php artisan serve
```

Untuk worker dan scheduler lokal:

```bash
php artisan queue:work redis --sleep=3 --tries=3
php artisan schedule:work
php artisan reverb:start
```

## Menjalankan dengan Docker

Pastikan Docker Engine dan Docker Compose tersedia, lalu:

```bash
cp .env.example .env
docker compose build
docker compose up -d mysql redis
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan jwt:secret
docker compose run --rm app php artisan migrate --seed
docker compose up -d
```

Akses layanan:

- Aplikasi web: `http://localhost:8080`
- Panel admin Filament: `http://localhost:8080/admin`
- API base URL: `http://localhost:8080/api/v1`
- Reverb websocket: `http://localhost:8081`
- MySQL dari host: `127.0.0.1:33060`
- Redis dari host: `127.0.0.1:63790`

Nilai database default Docker mengikuti `.env.example`:

```text
DB_DATABASE=gcms
DB_USERNAME=gcms
DB_PASSWORD=gcms_secret
```

## Akun Default Seeder

Seeder membuat akun awal berikut dengan password yang sama:

| Peran | Email | Password |
| --- | --- | --- |
| Super Admin | `superadmin@gcms.local` | `password` |
| Administrator | `admin@gcms.local` | `password` |
| Masyarakat | `masyarakat@gcms.local` | `password` |

Gunakan akun administrator/super admin untuk Filament `/admin` dan akun masyarakat untuk simulasi pembuatan pengaduan.

## API JWT

Base URL API:

```text
/api/v1
```

Login:

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "masyarakat@gcms.local",
  "password": "password"
}
```

Gunakan token dari `access_token`:

```http
Authorization: Bearer <token>
```

Endpoint utama:

- `POST /api/v1/auth/login`
- `POST /api/v1/auth/register`
- `GET /api/v1/auth/me`
- `GET /api/v1/complaints`
- `POST /api/v1/complaints`
- `GET /api/v1/complaints/{id}`
- `POST /api/v1/complaints/{id}/transition`
- `GET /api/v1/master/categories`
- `GET /api/v1/master/priorities`
- `GET /api/v1/master/statuses`

Dokumentasi lebih lengkap tersedia di `docs/api/API.md`.

## Filament Admin

Panel Filament aktif pada:

```text
/admin
```

Panel ini menggunakan guard web/session. Jalankan `php artisan migrate --seed`, lalu login dengan akun `admin@gcms.local` atau `superadmin@gcms.local`.

## Testing

Test suite menggunakan SQLite memory agar cepat dan terisolasi:

```bash
php artisan test
```

Atau di Docker:

```bash
docker compose run --rm app php artisan test
```

## Dokumentasi Lanjutan

- Arsitektur: `docs/architecture/SOFTWARE_ARCHITECTURE.md`
- ERD: `docs/database/ERD.md`
- Schema: `docs/database/SCHEMA.md`
- API: `docs/api/API.md`
- Instalasi: `docs/guides/INSTALLATION.md`
- Deployment: `docs/guides/DEPLOYMENT.md`
- Manual masyarakat: `docs/guides/USER_MANUAL.md`
- Panduan administrator: `docs/guides/ADMINISTRATOR_GUIDE.md`
