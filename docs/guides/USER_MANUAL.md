# Manual Pengguna Masyarakat

Manual ini ditujukan untuk masyarakat yang menggunakan GCMS untuk menyampaikan dan memantau pengaduan.

## Akses Aplikasi

Buka alamat aplikasi yang diberikan pemerintah daerah, misalnya:

```text
https://pengaduan.example.go.id
```

Untuk lingkungan lokal:

```text
http://localhost:8080
```

## Registrasi Akun

1. Buka halaman **Register**.
2. Isi nama, email, nomor telepon, NIK bila diminta, dan password.
3. Simpan formulir.
4. Login menggunakan email dan password.

Catatan:

- Gunakan email aktif karena notifikasi dapat dikirim melalui email.
- Simpan password dengan aman.

## Login

1. Buka halaman **Login**.
2. Masukkan email dan password.
3. Klik masuk.

Jika lupa password, gunakan fitur lupa password bila sudah dikonfigurasi oleh administrator.

## Membuat Pengaduan

1. Masuk ke portal.
2. Pilih menu **Pengaduan**.
3. Klik **Buat Pengaduan**.
4. Isi data berikut:
   - Kategori pengaduan.
   - Subkategori bila tersedia.
   - Judul singkat.
   - Deskripsi lengkap kejadian.
   - Alamat/lokasi.
   - Kecamatan/desa bila tersedia.
   - Nomor telepon atau email pelapor.
   - Lampiran bila fitur lampiran diaktifkan.
5. Pilih opsi anonim bila tidak ingin nama tampil kepada petugas tertentu.
6. Klik simpan/kirim.

Setelah berhasil, sistem akan membuat nomor tiket seperti:

```text
GCMS-20260716-0001
```

Simpan nomor tiket tersebut untuk pelacakan.

## Tips Menulis Pengaduan

- Gunakan judul jelas, misalnya "Jalan berlubang di depan Pasar Utama".
- Jelaskan waktu dan lokasi kejadian.
- Sertakan foto bila ada.
- Hindari data pribadi pihak lain yang tidak diperlukan.
- Pilih kategori yang paling sesuai agar pengaduan cepat diteruskan.

## Melacak Pengaduan

Pengaduan dapat dipantau melalui:

1. Menu **Pengaduan Saya** setelah login.
2. Halaman tracking publik dengan nomor tiket, bila tersedia.

Informasi yang dapat dilihat:

- Nomor tiket.
- Status saat ini.
- OPD atau petugas penanggung jawab bila ditampilkan.
- Timeline/kronologi.
- Tanggal dibuat.
- Estimasi SLA bila tersedia.

## Status Pengaduan

Status umum:

- **NEW**: pengaduan baru diterima.
- **VERIFIED**: pengaduan sudah diverifikasi.
- **IN_PROGRESS**: pengaduan sedang ditangani.
- **WAITING_APPROVAL**: hasil tindak lanjut menunggu persetujuan.
- **COMPLETED**: pengaduan sudah diselesaikan.
- **CLOSED**: pengaduan ditutup.
- **REJECTED/RETURNED**: pengaduan ditolak atau dikembalikan dengan alasan.

## Menanggapi Permintaan Perbaikan

Jika pengaduan dikembalikan karena data kurang:

1. Buka detail pengaduan.
2. Baca alasan pengembalian.
3. Perbaiki informasi yang diminta.
4. Kirim ulang atau hubungi kanal bantuan yang tersedia.

## Menutup Pengaduan

Jika masalah sudah selesai:

1. Buka detail pengaduan.
2. Periksa hasil tindak lanjut.
3. Klik tutup/konfirmasi selesai bila tombol tersedia.
4. Berikan rating kepuasan bila fitur tersedia.

## Keamanan Akun

- Jangan membagikan password.
- Logout setelah memakai komputer publik.
- Laporkan aktivitas mencurigakan kepada administrator.
- Pastikan alamat situs benar sebelum login.

## Bantuan

Hubungi admin pengaduan pemerintah daerah jika:

- Tidak bisa login.
- Tidak menerima email/OTP.
- Nomor tiket tidak ditemukan.
- Pengaduan salah kategori.
- Data pribadi perlu diperbarui.
