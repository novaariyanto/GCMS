# ERD GCMS

Diagram berikut menggambarkan entitas utama GCMS. Beberapa tabel pendukung seperti cache, sessions, dan password reset tidak ditampilkan agar fokus pada domain pengaduan.

```mermaid
erDiagram
    USERS ||--o{ COMPLAINTS : reports
    USERS ||--o{ LOGIN_HISTORIES : has
    USERS ||--o{ USER_DEVICES : has
    USERS ||--o{ OTP_CODES : receives
    USERS ||--o{ WORKFLOW_HISTORIES : acts
    USERS ||--o{ COMPLAINT_COMMENTS : writes
    USERS ||--o{ COMPLAINT_ATTACHMENTS : uploads

    PROVINCES ||--o{ REGENCIES : contains
    REGENCIES ||--o{ DISTRICTS : contains
    DISTRICTS ||--o{ VILLAGES : contains
    DISTRICTS ||--o{ COMPLAINTS : locates
    VILLAGES ||--o{ COMPLAINTS : locates

    OPDS ||--o{ DIVISIONS : has
    OPDS ||--o{ UNITS : has
    DIVISIONS ||--o{ UNITS : groups
    OPDS ||--o{ COMPLAINT_SUB_CATEGORIES : defaults
    OPDS ||--o{ WORKFLOW_NODES : owns
    OPDS ||--o{ COMPLAINTS : handles
    UNITS ||--o{ WORKFLOW_NODES : owns
    UNITS ||--o{ COMPLAINTS : handles

    COMPLAINT_CATEGORIES ||--o{ COMPLAINT_SUB_CATEGORIES : has
    COMPLAINT_CATEGORIES ||--o{ COMPLAINTS : classifies
    COMPLAINT_SUB_CATEGORIES ||--o{ COMPLAINTS : classifies
    PRIORITIES ||--o{ COMPLAINTS : sets_sla
    COMPLAINT_STATUSES ||--o{ COMPLAINTS : marks

    WORKFLOWS ||--o{ WORKFLOW_NODES : contains
    WORKFLOWS ||--o{ WORKFLOW_TRANSITIONS : contains
    WORKFLOW_NODES ||--o{ WORKFLOW_TRANSITIONS : from
    WORKFLOW_NODES ||--o{ WORKFLOW_TRANSITIONS : to
    WORKFLOW_ACTIONS ||--o{ WORKFLOW_TRANSITIONS : triggers
    COMPLAINT_STATUSES ||--o{ WORKFLOW_TRANSITIONS : targets

    WORKFLOWS ||--o{ COMPLAINTS : governs
    WORKFLOW_NODES ||--o{ COMPLAINTS : current
    COMPLAINTS ||--o{ WORKFLOW_HISTORIES : records
    WORKFLOW_NODES ||--o{ WORKFLOW_HISTORIES : from_to
    WORKFLOW_ACTIONS ||--o{ WORKFLOW_HISTORIES : action
    COMPLAINTS ||--o{ COMPLAINT_TIMELINES : has
    COMPLAINTS ||--o{ COMPLAINT_ATTACHMENTS : has
    COMPLAINTS ||--o{ COMPLAINT_COMMENTS : has
    COMPLAINT_ATTACHMENTS ||--o{ ATTACHMENT_DOWNLOADS : downloaded
    ATTACHMENT_TYPES ||--o{ COMPLAINT_ATTACHMENTS : types

    USERS {
        uuid id PK
        string name
        string email
        string password
        uuid opd_id FK
        uuid unit_id FK
        boolean is_active
    }

    COMPLAINTS {
        uuid id PK
        string ticket_number
        uuid reporter_id FK
        uuid category_id FK
        uuid sub_category_id FK
        uuid priority_id FK
        uuid status_id FK
        uuid workflow_id FK
        uuid current_node_id FK
        uuid current_opd_id FK
        uuid current_pic_id FK
        string title
        longText description
        timestamp sla_due_at
    }

    WORKFLOWS {
        uuid id PK
        string code
        string name
        boolean is_default
        boolean is_active
    }

    WORKFLOW_NODES {
        uuid id PK
        uuid workflow_id FK
        string code
        string name
        string role_name
        boolean is_start
        boolean is_end
    }

    WORKFLOW_TRANSITIONS {
        uuid id PK
        uuid workflow_id FK
        uuid from_node_id FK
        uuid to_node_id FK
        uuid action_id FK
        uuid target_status_id FK
        boolean is_active
    }
```

## Catatan Relasi Penting

- `complaints.current_node_id` menunjuk posisi workflow saat ini.
- `workflow_histories` menyimpan riwayat perpindahan node per pengaduan.
- `complaint_timelines` menyimpan narasi kronologis yang mudah ditampilkan kepada pengguna.
- `complaint_sub_categories.default_opd_id` membantu routing pengaduan ke OPD terkait.
- `workflow_transitions.target_status_id` menentukan status pengaduan setelah aksi dilakukan.
- Semua tabel domain utama memakai UUID sebagai primary key.
