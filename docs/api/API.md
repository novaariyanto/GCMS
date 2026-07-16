# Dokumentasi API GCMS

API GCMS berada di prefix:

```text
/api/v1
```

Format response menggunakan JSON. Endpoint privat memakai JWT Bearer Token dari endpoint login.

## Autentikasi JWT

### Login

```http
POST /api/v1/auth/login
Content-Type: application/json
```

Body:

```json
{
  "email": "masyarakat@gcms.local",
  "password": "password"
}
```

Response sukses:

```json
{
  "access_token": "<jwt>",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": "...",
    "name": "Masyarakat",
    "email": "masyarakat@gcms.local"
  }
}
```

Gunakan header berikut untuk endpoint privat:

```http
Authorization: Bearer <jwt>
Accept: application/json
```

### Register

```http
POST /api/v1/auth/register
```

Field umum:

- `name`
- `email`
- `password`
- `password_confirmation`
- `phone`
- `nik`

### Me

```http
GET /api/v1/auth/me
Authorization: Bearer <jwt>
```

Mengembalikan profil pengguna aktif.

### Logout

```http
POST /api/v1/auth/logout
Authorization: Bearer <jwt>
```

### Refresh Token

```http
POST /api/v1/auth/refresh
Authorization: Bearer <jwt>
```

### OTP dan Reset Password

```http
POST /api/v1/auth/forgot-password
POST /api/v1/auth/otp/send
POST /api/v1/auth/otp/verify
```

## Pengaduan

Semua endpoint pengaduan membutuhkan JWT.

### Daftar Pengaduan

```http
GET /api/v1/complaints
Authorization: Bearer <jwt>
```

Query:

- `q` - cari nomor tiket atau judul.
- `status_id` - filter status.
- `category_id` - filter kategori.
- `per_page` - jumlah data per halaman, default 15.

### Buat Pengaduan

```http
POST /api/v1/complaints
Authorization: Bearer <jwt>
Content-Type: application/json
```

Body minimum:

```json
{
  "category_id": "uuid-kategori",
  "title": "Jalan rusak di depan pasar",
  "description": "Lubang besar membahayakan pengendara.",
  "address": "Jl. Merdeka No. 10",
  "is_anonymous": false
}
```

Field opsional:

- `sub_category_id`
- `priority_id`
- `district_id`
- `village_id`
- `latitude`
- `longitude`
- `reporter_name`
- `reporter_phone`
- `reporter_email`

Response sukses berisi detail pengaduan, nomor tiket, status awal, workflow, dan node saat ini.

### Detail Pengaduan

```http
GET /api/v1/complaints/{id}
Authorization: Bearer <jwt>
```

### Update Pengaduan

```http
PUT /api/v1/complaints/{id}
PATCH /api/v1/complaints/{id}
Authorization: Bearer <jwt>
```

Menggunakan validasi yang sama dengan pembuatan pengaduan.

### Hapus Pengaduan

```http
DELETE /api/v1/complaints/{id}
Authorization: Bearer <jwt>
```

### Timeline Pengaduan

```http
GET /api/v1/complaints/{id}/timeline
Authorization: Bearer <jwt>
```

### Workflow History Pengaduan

```http
GET /api/v1/complaints/{id}/histories
Authorization: Bearer <jwt>
```

### Transisi Workflow

```http
POST /api/v1/complaints/{id}/transition
Authorization: Bearer <jwt>
Content-Type: application/json
```

Body dengan action:

```json
{
  "action_code": "VERIFY",
  "remark": "Pengaduan valid dan siap diteruskan."
}
```

Atau dengan ID transisi:

```json
{
  "transition_id": "uuid-transition",
  "remark": "Diproses sesuai kewenangan."
}
```

Action yang umum:

- `VERIFY`
- `FORWARD`
- `ASSIGN`
- `APPROVE`
- `CLOSE`
- `RETURN`
- `REJECT`

## Workflow

```http
GET /api/v1/workflows
GET /api/v1/workflows/{id}
Authorization: Bearer <jwt>
```

Mengembalikan workflow aktif, node, aksi, dan transisi.

## Master Data

```http
GET /api/v1/master/categories
GET /api/v1/master/priorities
GET /api/v1/master/statuses
GET /api/v1/master/opds
GET /api/v1/master/regions
GET /api/v1/master/districts
GET /api/v1/master/villages
Authorization: Bearer <jwt>
```

Endpoint master dipakai oleh form pengaduan, filter dashboard, dan administrasi.

## Notifikasi

```http
GET /api/v1/notifications
POST /api/v1/notifications/{id}/read
Authorization: Bearer <jwt>
```

## Dashboard dan Laporan

```http
GET /api/v1/dashboard/stats
GET /api/v1/reports/complaints
Authorization: Bearer <jwt>
```

## User Management

```http
GET /api/v1/users
POST /api/v1/users
GET /api/v1/users/{id}
PUT /api/v1/users/{id}
PATCH /api/v1/users/{id}
DELETE /api/v1/users/{id}
Authorization: Bearer <jwt>
```

## Search

```http
GET /api/v1/search?q=jalan
Authorization: Bearer <jwt>
```

## Status Error Umum

- `401 Unauthorized` - token tidak ada, tidak valid, atau kedaluwarsa.
- `403 Forbidden` - user tidak punya akses.
- `404 Not Found` - resource tidak ditemukan.
- `422 Unprocessable Entity` - validasi gagal.
- `500 Internal Server Error` - kesalahan server.

## Akun Contoh

Setelah `php artisan migrate --seed`:

- `superadmin@gcms.local` / `password`
- `admin@gcms.local` / `password`
- `masyarakat@gcms.local` / `password`
