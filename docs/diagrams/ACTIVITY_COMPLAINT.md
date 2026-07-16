# Activity Diagram Pengaduan

Alur berikut menunjukkan proses dari pembuatan pengaduan sampai ditutup.

```mermaid
flowchart TD
    A([Mulai]) --> B[Masyarakat login atau mengisi kanal pengaduan]
    B --> C[Isi kategori, lokasi, judul, deskripsi, kontak, dan lampiran bila ada]
    C --> D{Validasi input berhasil?}
    D -- Tidak --> E[Tampilkan error validasi]
    E --> C
    D -- Ya --> F[Buat nomor tiket]
    F --> G[Pilih workflow default/kategori]
    G --> H[Set status NEW dan node START]
    H --> I[Catat timeline created dan workflow history START]
    I --> J[Kirim notifikasi pengaduan baru]
    J --> K[Admin OPD/Diskominfo meninjau]
    K --> L{Pengaduan valid?}
    L -- Tidak --> M[Reject atau Return dengan alasan]
    M --> N[Notifikasi alasan ke masyarakat]
    N --> Z([Selesai sementara])
    L -- Ya --> O[Verifikasi pengaduan]
    O --> P[Status VERIFIED dan node VERIFY]
    P --> Q[Teruskan ke OPD terkait]
    Q --> R[Status IN_PROGRESS dan node OPD]
    R --> S{Perlu petugas khusus?}
    S -- Ya --> T[Assign ke petugas]
    T --> U[Petugas menindaklanjuti]
    S -- Tidak --> U
    U --> V[Petugas/Admin mengirim hasil tindak lanjut]
    V --> W[Admin OPD review hasil]
    W --> X{Hasil disetujui?}
    X -- Tidak --> Y[Kembalikan ke petugas/OPD dengan catatan]
    Y --> U
    X -- Ya --> AA[Set status COMPLETED]
    AA --> AB[Masyarakat/admin menutup pengaduan]
    AB --> AC[Set status CLOSED dan closed_at]
    AC --> AD[Catat audit, timeline, dan notifikasi akhir]
    AD --> AE([Selesai])
```

## Titik Kontrol

- Validasi input dilakukan oleh `StoreComplaintRequest`.
- Nomor tiket dibuat di `ComplaintService`.
- Transisi workflow dilakukan oleh `WorkflowEngine`.
- Setiap perubahan status menghasilkan `workflow_histories` dan `complaint_timelines`.
- Event domain dapat memicu notifikasi asynchronous lewat queue.
