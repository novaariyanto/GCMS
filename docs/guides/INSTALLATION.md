# Panduan Instalasi GCMS

Panduan ini menjelaskan instalasi GCMS untuk pengembangan lokal.

## Prasyarat

### Opsi Lokal

- PHP 8.2+.
- Composer 2.
- Node.js 20+ dan npm.
- MySQL 8+.
- Redis 7+.
- Ekstensi PHP: `pdo_mysql`, `redis`, `gd`, `zip`, `bcmath`, `intl`, `opcache`.

### Opsi Docker

- Docker Engine.
- Docker Compose v2.

## Instalasi Lokal

1. Clone repository dan masuk ke direktori project.

2. Salin file environment.

   ```bash
   cp .env.example .env
   ```

3. Sesuaikan koneksi database dan Redis di `.env`.

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gcms
   DB_USERNAME=gcms
   DB_PASSWORD=gcms_secret

   REDIS_HOST=127.0.0.1
   CACHE_STORE=redis
   QUEUE_CONNECTION=redis
   SESSION_DRIVER=redis
   ```

4. Install dependency.

   ```bash
   composer install
   npm install
   ```

5. Buat key aplikasi dan JWT.

   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

6. Jalankan migration dan seeder.

   ```bash
   php artisan migrate --seed
   ```

7. Build asset frontend.

   ```bash
   npm run build
   ```

8. Jalankan server.

   ```bash
   php artisan serve
   ```

9. Jalankan worker tambahan pada terminal berbeda.

   ```bash
   php artisan queue:work redis --sleep=3 --tries=3
   php artisan schedule:work
   php artisan reverb:start
   ```

## Instalasi dengan Docker

1. Salin environment.

   ```bash
   cp .env.example .env
   ```

2. Build image.

   ```bash
   docker compose build
   ```

3. Naikkan database dan Redis.

   ```bash
   docker compose up -d mysql redis
   ```

4. Install dependency dan siapkan aplikasi.

   ```bash
   docker compose run --rm app composer install
   docker compose run --rm app php artisan key:generate
   docker compose run --rm app php artisan jwt:secret
   docker compose run --rm app php artisan migrate --seed
   docker compose run --rm app npm install
   docker compose run --rm app npm run build
   ```

5. Jalankan seluruh service.

   ```bash
   docker compose up -d
   ```

6. Buka aplikasi.

   - Web: `http://localhost:8080`
   - Admin: `http://localhost:8080/admin`
   - API: `http://localhost:8080/api/v1`
   - Reverb: `http://localhost:8081`

## Akun Default

| Email | Password | Keterangan |
| --- | --- | --- |
| `superadmin@gcms.local` | `password` | akses penuh |
| `admin@gcms.local` | `password` | administrator |
| `masyarakat@gcms.local` | `password` | pelapor |

## Verifikasi Instalasi

Jalankan:

```bash
php artisan about
php artisan route:list
php artisan test
```

Untuk Docker:

```bash
docker compose run --rm app php artisan test
```

## Troubleshooting

### JWT Secret belum dibuat

Gejala: login API gagal karena token tidak bisa dibuat.

Solusi:

```bash
php artisan jwt:secret
```

### Permission storage/cache

Gejala: error menulis cache/view/log.

Solusi:

```bash
chmod -R ug+rw storage bootstrap/cache
```

### Database belum siap di Docker

Gejala: migration gagal karena koneksi MySQL ditolak.

Solusi:

```bash
docker compose ps
docker compose logs mysql
docker compose run --rm app php artisan migrate --seed
```

### Redis tidak terhubung

Pastikan `.env` memakai `REDIS_HOST=redis` di Docker dan `REDIS_HOST=127.0.0.1` untuk lokal host.
