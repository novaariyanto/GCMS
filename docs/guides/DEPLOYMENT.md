# Panduan Deployment GCMS

Dokumen ini menjelaskan praktik deployment GCMS untuk environment staging atau production.

## Prinsip Production

- Gunakan environment variable, bukan nilai hard-coded.
- Jalankan migration secara terkontrol.
- Pisahkan proses web, queue, scheduler, dan websocket.
- Aktifkan cache konfigurasi dan route.
- Simpan log di storage terpusat.
- Backup database dan storage lampiran secara berkala.
- Gunakan TLS/HTTPS di reverse proxy publik.

## Komponen Runtime

| Komponen | Fungsi |
| --- | --- |
| Nginx | Reverse proxy HTTP ke PHP-FPM |
| PHP-FPM app | Menjalankan request Laravel |
| MySQL 8.4 | Database utama |
| Redis | Cache, session, queue |
| Queue worker | Memproses job notifikasi dan side effect |
| Scheduler | Menjalankan task periodik Laravel |
| Reverb | Websocket/realtime event |
| Object storage/local disk | Lampiran pengaduan |

## Environment Variable Penting

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gcms.example.go.id

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=gcms
DB_USERNAME=gcms
DB_PASSWORD=change-me

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=gcms
REVERB_APP_KEY=change-me
REVERB_APP_SECRET=change-me
REVERB_HOST=gcms.example.go.id
REVERB_PORT=443
REVERB_SCHEME=https

JWT_SECRET=generated-secret
MAIL_MAILER=smtp
```

Generate secret sebelum deploy:

```bash
php artisan key:generate --show
php artisan jwt:secret --show
```

## Build Image

```bash
docker compose build app
```

Untuk registry:

```bash
docker tag gcms-app:local registry.example.go.id/gcms/app:2026-07-16
docker push registry.example.go.id/gcms/app:2026-07-16
```

## Langkah Deployment

1. Pull image terbaru.
2. Pastikan `.env` production tersedia di secret manager atau server.
3. Jalankan maintenance mode bila deploy menyebabkan downtime.

   ```bash
   php artisan down --render="errors::503"
   ```

4. Jalankan migration.

   ```bash
   php artisan migrate --force
   ```

5. Optimasi framework.

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

6. Restart proses.

   ```bash
   php artisan queue:restart
   ```

7. Matikan maintenance mode.

   ```bash
   php artisan up
   ```

## Deployment dengan Docker Compose

Contoh minimal:

```bash
docker compose pull
docker compose up -d mysql redis
docker compose run --rm app php artisan migrate --force
docker compose run --rm app php artisan config:cache
docker compose run --rm app php artisan route:cache
docker compose up -d app nginx queue scheduler reverb
```

## Queue Worker

Queue worker perlu berjalan terus:

```bash
php artisan queue:work redis --sleep=3 --tries=3 --timeout=90
```

Rekomendasi:

- Gunakan minimal 2 worker untuk production.
- Monitor job gagal.
- Jalankan `php artisan queue:restart` setelah deploy.
- Pertimbangkan Horizon bila kebutuhan monitoring queue meningkat.

## Scheduler

Gunakan salah satu pendekatan:

1. `php artisan schedule:work` sebagai service container.
2. Cron host:

   ```cron
   * * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
   ```

## Reverb

Reverb harus dapat diakses oleh browser/client:

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

Jika memakai reverse proxy TLS, pastikan header websocket diteruskan:

- `Upgrade`
- `Connection`
- `Host`
- `X-Forwarded-Proto`

## Storage dan Lampiran

Untuk storage lokal:

```bash
php artisan storage:link
```

Untuk production skala besar, gunakan object storage yang kompatibel S3 dan set:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
```

## Backup

Backup wajib:

- Database MySQL.
- Direktori `storage/app` bila memakai local disk.
- File `.env` production atau secret manager.

Contoh backup database:

```bash
mysqldump -h mysql -u gcms -p gcms > gcms-$(date +%F).sql
```

## Health Check Manual

```bash
curl -I https://gcms.example.go.id
curl https://gcms.example.go.id/api/v1/master/statuses \
  -H "Authorization: Bearer <token>"
```

Periksa juga:

- `storage/logs/laravel.log`
- status queue worker
- status scheduler
- koneksi Redis
- koneksi Reverb

## Rollback

1. Aktifkan maintenance mode.
2. Kembalikan image sebelumnya.
3. Jalankan rollback migration hanya bila aman dan sudah diuji.
4. Restore database dari backup bila data migration tidak kompatibel.
5. Restart service dan queue.

Rollback migration destructive harus diperlakukan sebagai operasi berisiko tinggi karena dapat menghapus data.
