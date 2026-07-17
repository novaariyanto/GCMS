# Panduan Administrator GCMS

Panduan ini ditujukan untuk administrator dan super admin yang mengelola GCMS melalui panel web dan Filament.

## Akses Admin

Panel admin Filament:

```text
/admin
```

Akun seed awal:

| Email | Password | Peran |
| --- | --- | --- |
| `superadmin@gcms.local` | `password` | Super Admin |
| `admin@gcms.local` | `password` | Administrator |

Segera ganti password default setelah setup production.

## Tanggung Jawab Administrator

- Mengelola user, role, dan permission.
- Mengelola OPD, unit, kategori, subkategori, prioritas, dan status.
- Mengawasi workflow pengaduan.
- Memantau dashboard dan laporan.
- Memeriksa pengaduan bermasalah atau melewati SLA.
- Memantau queue, notifikasi, audit log, dan error aplikasi.

## Setup Awal

1. Jalankan migration dan seeder.

   ```bash
   php artisan migrate --seed
   ```

2. Login sebagai super admin.
3. Ubah password akun default.
4. Validasi master data:
   - OPD.
   - Unit.
   - Kategori dan subkategori.
   - Prioritas dan SLA.
   - Status pengaduan.
   - Wilayah.
5. Pastikan workflow default aktif.
6. Uji pembuatan pengaduan dengan akun masyarakat.
7. Uji transisi `VERIFY`, `FORWARD`, `ASSIGN`, `APPROVE`, dan `CLOSE`.

## Mengelola User dan Role

Role standar:

- **SUPER_ADMIN**: akses penuh.
- **ADMINISTRATOR**: administrasi aplikasi dan laporan.
- **ADMIN_OPD**: verifikasi, penerusan, dan pengelolaan pengaduan OPD.
- **ADMIN_UNIT**: pengelolaan pengaduan unit.
- **SUPERVISOR**: supervisi tindak lanjut.
- **PETUGAS**: penanganan pengaduan.
- **MASYARAKAT**: pelapor.

Praktik baik:

- Berikan akses minimum yang diperlukan.
- Nonaktifkan user yang pindah tugas.
- Jangan memakai akun bersama.
- Audit perubahan role secara berkala.

## Mengelola Master Data

### OPD dan Unit

Pastikan setiap OPD punya:

- kode unik,
- nama resmi,
- email kontak,
- status aktif.

Unit digunakan untuk pembagian tugas lebih rinci.

### Kategori dan Subkategori

Kategori membantu masyarakat memilih topik. Subkategori dapat diarahkan ke `default_opd_id` agar routing lebih cepat.

Contoh:

- Infrastruktur -> Jalan Rusak -> PUPR.
- Kesehatan -> Layanan Puskesmas -> Dinas Kesehatan.
- Pelayanan Publik -> Informasi Publik -> Diskominfo.

### Prioritas dan SLA

Prioritas seed:

- LOW: 168 jam.
- MEDIUM: 72 jam.
- HIGH: 48 jam.
- CRITICAL: 24 jam.

Sesuaikan SLA dengan kebijakan daerah.

## Workflow Operasional

Workflow default:

1. Masyarakat membuat pengaduan.
2. Diskominfo/Admin OPD melakukan verifikasi.
3. Pengaduan diteruskan ke OPD terkait.
4. OPD menugaskan petugas bila diperlukan.
5. Petugas menyelesaikan tindak lanjut.
6. Admin OPD menyetujui hasil.
7. Pengaduan ditutup.

Action penting:

- `VERIFY`: validasi awal.
- `FORWARD`: teruskan ke node/OPD berikutnya.
- `ASSIGN`: tugaskan user/petugas.
- `APPROVE`: setujui hasil dan lanjutkan status.
- `CLOSE`: tutup pengaduan.
- `RETURN`: kembalikan untuk perbaikan.
- `REJECT`: tolak bila tidak valid.

## Monitoring Harian

Periksa:

- Jumlah pengaduan baru.
- Pengaduan melewati SLA.
- Pengaduan tanpa PIC.
- Queue job gagal.
- Notifikasi gagal.
- Error di `storage/logs/laravel.log`.
- Login gagal berulang di `login_histories`.

Command berguna:

```bash
php artisan queue:failed
php artisan queue:retry all
php artisan route:list
php artisan about
```

## Laporan

Laporan pengaduan sebaiknya dipakai untuk:

- tren kategori,
- performa OPD,
- kepatuhan SLA,
- wilayah dengan pengaduan tinggi,
- beban petugas,
- status penyelesaian.

Endpoint laporan tersedia di:

```text
GET /api/v1/reports/complaints
GET /api/v1/dashboard/stats
```

## Backup dan Retensi

Minimal backup:

- Database harian.
- Storage lampiran.
- Environment/secret.

Retensi audit dan pengaduan harus mengikuti kebijakan pemerintah daerah dan peraturan perlindungan data.

## Keamanan

- Gunakan HTTPS.
- Set `APP_DEBUG=false` di production.
- Ganti semua password default.
- Simpan secret di secret manager.
- Rotasi JWT secret hanya dengan rencana logout massal.
- Batasi akses panel admin dengan jaringan/VPN bila memungkinkan.
- Monitor login gagal dan IP mencurigakan.

## Troubleshooting

### Pengaduan tidak bisa dibuat

Periksa:

- kategori aktif tersedia,
- prioritas aktif tersedia,
- workflow default aktif,
- node start tersedia,
- tabel status sudah ter-seed.

### Transisi workflow gagal

Periksa:

- action sesuai node saat ini,
- capability node mengizinkan action,
- transition aktif tersedia,
- target status tersedia,
- remark diisi bila action mewajibkan remark.

### Notifikasi tidak terkirim

Periksa:

- queue worker berjalan,
- konfigurasi mail/WhatsApp/SMS,
- tabel `notification_logs`,
- log Laravel.

### Panel admin tidak bisa diakses

Periksa:

- user aktif,
- password benar,
- role admin tersedia,
- session driver aktif,
- route `/admin` terdaftar.
