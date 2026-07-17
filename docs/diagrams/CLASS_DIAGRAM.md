# Class Diagram Inti GCMS

Diagram berikut menampilkan kelas inti pada domain pengaduan, workflow, auth, dan service aplikasi.

```mermaid
classDiagram
    class User {
        +uuid id
        +string name
        +string email
        +bool is_active
        +getJWTIdentifier()
        +getJWTCustomClaims()
    }

    class Complaint {
        +uuid id
        +string ticket_number
        +string title
        +string description
        +bool is_anonymous
        +datetime sla_due_at
        +reporter()
        +status()
        +currentNode()
        +workflowHistories()
    }

    class ComplaintCategory {
        +string code
        +string name
    }

    class ComplaintSubCategory {
        +string code
        +string name
        +uuid default_opd_id
    }

    class Priority {
        +string code
        +int level
        +int sla_hours
    }

    class ComplaintStatus {
        +string code
        +string name
        +bool is_final
    }

    class Workflow {
        +string code
        +string name
        +bool is_default
        +nodes()
        +transitions()
    }

    class WorkflowNode {
        +string code
        +string role_name
        +bool is_start
        +bool is_end
        +bool can_forward
        +bool can_assign
        +bool can_approve
        +bool can_close
    }

    class WorkflowAction {
        +string code
        +string label
        +bool requires_remark
        +bool requires_attachment
    }

    class WorkflowTransition {
        +uuid from_node_id
        +uuid to_node_id
        +uuid action_id
        +uuid target_status_id
    }

    class WorkflowHistory {
        +string action_code
        +string remark
        +datetime started_at
        +datetime completed_at
    }

    class CreateComplaintData {
        +string category_id
        +string priority_id
        +string title
        +string description
        +string reporter_id
    }

    class TransitionComplaintData {
        +string complaint_id
        +string action_code
        +string remark
        +string to_user_id
    }

    class ComplaintService {
        +create(CreateComplaintData) Complaint
        -generateTicketNumber() string
        -resolveWorkflow(CreateComplaintData) Workflow
    }

    class WorkflowEngine {
        +transition(TransitionComplaintData) Complaint
        +availableActions(Complaint) Collection
        -resolveTransition(Complaint, string) WorkflowTransition
    }

    class AuthService {
        +login(string, string, string, string) array
        +register(array) User
        +logout(string) void
    }

    class WorkflowRepository {
        +findDefault() Workflow
        +findForCategory(string, string) Workflow
        +getTransitionsFromNode(string) Collection
    }

    User "1" --> "many" Complaint : reports
    Complaint "many" --> "1" ComplaintCategory
    Complaint "many" --> "0..1" ComplaintSubCategory
    Complaint "many" --> "1" Priority
    Complaint "many" --> "1" ComplaintStatus
    Complaint "many" --> "0..1" Workflow
    Complaint "many" --> "0..1" WorkflowNode : current
    Complaint "1" --> "many" WorkflowHistory
    Workflow "1" --> "many" WorkflowNode
    Workflow "1" --> "many" WorkflowTransition
    WorkflowTransition "many" --> "1" WorkflowNode : from
    WorkflowTransition "many" --> "1" WorkflowNode : to
    WorkflowTransition "many" --> "1" WorkflowAction
    WorkflowTransition "many" --> "0..1" ComplaintStatus
    ComplaintService --> CreateComplaintData
    ComplaintService --> WorkflowRepository
    WorkflowEngine --> TransitionComplaintData
    WorkflowEngine --> WorkflowRepository
    AuthService --> User
```

## Catatan Desain

- DTO menjaga input use case tetap eksplisit.
- Service aplikasi mengatur transaksi dan event domain.
- Model domain menyimpan relasi dan state utama.
- Repository melokalisasi query workflow yang dipakai service.
