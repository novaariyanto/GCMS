# Schema Database GCMS

Dokumen ini merangkum tabel utama, fungsi, dan field kunci. Detail final tetap mengikuti migration di `database/migrations`.

## Konvensi

- Primary key domain menggunakan `uuid id`.
- Banyak tabel memakai `timestamps` dan `softDeletes`.
- Field `code` biasanya unik dan dipakai sebagai stable identifier bisnis.
- Foreign key dibuat eksplisit untuk menjaga integritas data.

## Auth dan Akses

### `users`

Menyimpan akun masyarakat, admin, petugas, dan pengguna internal.

Field penting:

- `id`
- `name`
- `email`
- `phone`
- `nik`
- `password`
- `opd_id`
- `unit_id`
- `is_active`
- `last_login_at`
- `preferences`

### `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

Tabel dari Spatie Permission. GCMS memakai role untuk mengatur akses web/admin dan permission granular.

### `login_histories`

Mencatat percobaan login.

Field penting:

- `user_id`
- `email`
- `ip_address`
- `user_agent`
- `status`
- `failure_reason`

### `otp_codes`

Menyimpan OTP untuk login, registrasi, atau reset.

Field penting:

- `user_id`
- `channel`
- `destination`
- `purpose`
- `code`
- `expires_at`
- `verified_at`
- `attempts`

### `user_devices`

Menyimpan fingerprint/perangkat pengguna.

## Master Wilayah

### `provinces`

Field: `code`, `name`, `is_active`.

### `regencies`

Field: `province_id`, `code`, `name`, `type`, `is_active`.

### `districts`

Field: `regency_id`, `code`, `name`, `is_active`.

### `villages`

Field: `district_id`, `code`, `name`, `type`, `is_active`.

## Organisasi Pemerintah

### `opds`

Organisasi perangkat daerah.

Field penting:

- `code`
- `name`
- `short_name`
- `description`
- `phone`
- `email`
- `address`
- `is_active`

### `divisions`

Sub struktur OPD.

Field: `opd_id`, `code`, `name`, `description`, `is_active`.

### `units`

Unit kerja di bawah OPD/divisi.

Field: `opd_id`, `division_id`, `code`, `name`, `description`, `is_active`.

## Master Pengaduan

### `complaint_categories`

Kategori utama pengaduan.

Field: `code`, `name`, `description`, `icon`, `sort_order`, `is_active`.

### `complaint_sub_categories`

Subkategori dengan OPD default.

Field:

- `category_id`
- `default_opd_id`
- `code`
- `name`
- `sort_order`
- `is_active`

### `priorities`

Prioritas dan SLA ringkas.

Field:

- `code`
- `name`
- `color`
- `level`
- `sla_hours`
- `is_active`

### `complaint_statuses`

Status lifecycle pengaduan.

Field:

- `code`
- `name`
- `color`
- `is_final`
- `sort_order`
- `is_active`

### `slas`

Definisi SLA tambahan berbasis prioritas/kategori.

Field: `code`, `name`, `priority_id`, `category_id`, `response_hours`, `resolution_hours`, `is_active`.

## Workflow

### `workflows`

Definisi workflow pengaduan.

Field:

- `code`
- `name`
- `description`
- `category_id`
- `sub_category_id`
- `is_default`
- `is_active`

### `workflow_nodes`

Node/tahap dalam workflow.

Field penting:

- `workflow_id`
- `code`
- `name`
- `opd_id`
- `unit_id`
- `role_name`
- `sequence`
- `is_start`
- `is_end`
- capability flags: `can_forward`, `can_assign`, `can_return`, `can_reject`, `can_approve`, `can_close`, dan lainnya.

### `workflow_actions`

Master aksi workflow.

Field:

- `code`
- `name`
- `label`
- `color`
- `requires_remark`
- `requires_attachment`
- `is_active`
- `sort_order`

### `workflow_transitions`

Relasi node asal, node tujuan, aksi, dan status target.

Field:

- `workflow_id`
- `from_node_id`
- `to_node_id`
- `action_id`
- `target_status_id`
- `conditions`
- `is_active`

### `workflow_histories`

Audit perpindahan workflow per pengaduan.

Field:

- `complaint_id`
- `from_user_id`
- `to_user_id`
- `from_role`
- `to_role`
- `from_node_id`
- `to_node_id`
- `action_id`
- `action_code`
- `remark`
- `started_at`
- `completed_at`
- `processing_seconds`

## Transaksi Pengaduan

### `complaints`

Tabel pusat domain pengaduan.

Field penting:

- `ticket_number`
- `reporter_id`
- `category_id`
- `sub_category_id`
- `priority_id`
- `status_id`
- `workflow_id`
- `current_node_id`
- `current_opd_id`
- `current_unit_id`
- `current_role`
- `current_pic_id`
- `district_id`
- `village_id`
- `title`
- `description`
- `address`
- `latitude`
- `longitude`
- `reporter_name`
- `reporter_phone`
- `reporter_email`
- `is_anonymous`
- `sla_due_at`
- `responded_at`
- `resolved_at`
- `closed_at`
- `satisfaction_rating`

### `complaint_timelines`

Kronologi untuk tampilan pengguna.

Field: `complaint_id`, `user_id`, `event_type`, `title`, `description`, `meta`, `occurred_at`.

### `complaint_attachments`

Lampiran pengaduan.

Field: `complaint_id`, `uploaded_by`, `attachment_type_id`, `disk`, `path`, `original_name`, `mime_type`, `size`, `version`, `is_public`.

### `complaint_comments`

Komentar internal/publik.

Field: `complaint_id`, `user_id`, `body`, `is_internal`, `mentions`, `read_at`.

### `attachment_downloads`

Audit unduhan lampiran.

Field: `attachment_id`, `user_id`, `ip_address`, `user_agent`.

## Konfigurasi, Audit, dan Notifikasi

### `regional_settings`

Identitas aplikasi dan pemerintah daerah.

### `attachment_types`

Jenis lampiran, ekstensi yang diizinkan, dan ukuran maksimal.

### `notification_media`

Master media notifikasi seperti email, WhatsApp, SMS, dan in-app.

### `notification_logs`

Riwayat pengiriman notifikasi.

### `notifications`

Tabel notification Laravel untuk notifikasi database.

### `audit_logs`

Audit aksi penting.

Field: `user_id`, `action`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `description`.

### `working_hours` dan `holidays`

Kalender operasional untuk SLA dan pelaporan.
