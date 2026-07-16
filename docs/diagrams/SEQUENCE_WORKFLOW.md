# Sequence Diagram Transisi Workflow

Diagram ini menjelaskan alur ketika pengguna melakukan aksi workflow, misalnya `VERIFY`, `FORWARD`, `ASSIGN`, `APPROVE`, atau `CLOSE`.

```mermaid
sequenceDiagram
    actor User as Pengguna API
    participant API as ComplaintController
    participant Request as TransitionComplaintRequest
    participant DTO as TransitionComplaintData
    participant Engine as WorkflowEngine
    participant Repo as WorkflowRepository
    participant DB as Database
    participant Event as Event Dispatcher
    participant Listener as Listener/Notification

    User->>API: POST /api/v1/complaints/{id}/transition
    API->>Request: Validasi action_code / transition_id / remark
    Request-->>API: Data valid
    API->>DTO: fromArray(data + complaint_id)
    API->>Engine: transition(dto)
    Engine->>DB: BEGIN TRANSACTION
    Engine->>DB: Lock complaint + load currentNode/status
    Engine->>Engine: Cek nodeAllowsAction()
    Engine->>Repo: getTransitionsFromNode(current_node_id)
    Repo->>DB: Query active workflow_transitions
    DB-->>Repo: Transisi aktif + action + target status
    Repo-->>Engine: Collection transisi
    Engine->>Engine: Pilih transisi sesuai action_code
    Engine->>Engine: Validasi remark/attachment bila wajib
    Engine->>DB: Update complaint current node/status/PIC/OPD
    Engine->>DB: Insert workflow_histories
    Engine->>DB: Insert complaint_timelines
    Engine->>DB: COMMIT
    Engine->>Event: ComplaintTransitioned
    Event->>Listener: Handle event
    Listener->>DB: Simpan log/notifikasi
    Engine-->>API: Complaint terbaru
    API-->>User: ComplaintDetailResource JSON
```

## Validasi Utama

- `action_code` wajib bila `transition_id` tidak dikirim.
- Action harus didukung capability node saat ini, misalnya `can_forward`.
- Transition aktif harus tersedia dari node saat ini.
- Remark wajib untuk action yang ditandai `requires_remark`.
- Target user harus ada bila `to_user_id` dikirim.

## Efek Data

Setelah transisi berhasil:

- `complaints.current_node_id` berubah.
- `complaints.status_id` berubah bila transition punya target status.
- `complaints.responded_at`, `resolved_at`, atau `closed_at` dapat terisi.
- `workflow_histories` bertambah satu record.
- `complaint_timelines` bertambah satu record.
- Event `ComplaintTransitioned` dipublikasikan.
