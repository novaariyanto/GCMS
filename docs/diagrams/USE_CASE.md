# Diagram Use Case GCMS

Diagram ini merangkum interaksi aktor utama dengan sistem.

```mermaid
flowchart LR
    M[Masyarakat]
    AO[Admin OPD]
    P[Petugas]
    A[Administrator]
    SA[Super Admin]
    SYS[Scheduler / Queue]

    subgraph GCMS[GCMS]
        UC1((Registrasi akun))
        UC2((Login JWT / Web))
        UC3((Buat pengaduan))
        UC4((Lacak tiket))
        UC5((Lihat daftar pengaduan))
        UC6((Verifikasi pengaduan))
        UC7((Teruskan ke OPD))
        UC8((Tugaskan petugas))
        UC9((Kerjakan / beri tanggapan))
        UC10((Setujui penyelesaian))
        UC11((Tutup pengaduan))
        UC12((Kelola master data))
        UC13((Kelola user dan role))
        UC14((Lihat dashboard dan laporan))
        UC15((Kirim notifikasi))
        UC16((Audit aktivitas))
    end

    M --> UC1
    M --> UC2
    M --> UC3
    M --> UC4
    M --> UC5

    AO --> UC2
    AO --> UC5
    AO --> UC6
    AO --> UC7
    AO --> UC8
    AO --> UC10
    AO --> UC14

    P --> UC2
    P --> UC5
    P --> UC9

    A --> UC12
    A --> UC13
    A --> UC14

    SA --> UC12
    SA --> UC13
    SA --> UC14

    SYS --> UC15
    SYS --> UC16

    UC3 --> UC15
    UC6 --> UC16
    UC7 --> UC15
    UC8 --> UC15
    UC10 --> UC15
    UC11 --> UC16
```

## Aktor

- **Masyarakat**: pelapor, membuat pengaduan, memantau status, dan melihat riwayat.
- **Admin OPD**: melakukan verifikasi, meneruskan, menugaskan, dan menyetujui pengaduan.
- **Petugas**: menangani pengaduan teknis dan mengirim hasil tindak lanjut.
- **Administrator**: mengelola master data, user, role, dashboard, dan laporan.
- **Super Admin**: mengelola konfigurasi penuh lintas modul.
- **Scheduler/Queue**: menjalankan pekerjaan latar seperti notifikasi, SLA reminder, dan audit async.

## Use Case Kritis

1. Buat pengaduan menghasilkan nomor tiket dan workflow history awal.
2. Verifikasi memastikan pengaduan valid sebelum diteruskan.
3. Penugasan menentukan PIC atau unit pelaksana.
4. Persetujuan dan penutupan mengakhiri lifecycle.
5. Dashboard dan laporan dipakai pimpinan untuk memantau tren dan SLA.
