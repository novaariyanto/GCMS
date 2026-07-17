<?php

namespace Database\Seeders;

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Models\ComplaintStatus;
use App\Domain\Organization\Models\Opd;
use App\Domain\Workflow\Enums\WorkflowActionCode;
use App\Domain\Workflow\Models\Workflow;
use App\Domain\Workflow\Models\WorkflowAction;
use App\Domain\Workflow\Models\WorkflowNode;
use App\Domain\Workflow\Models\WorkflowTransition;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Opd::query()->where('code', 'DISKOMINFO')->firstOrFail();

        $workflow = Workflow::query()->firstOrCreate(
            ['code' => 'DEFAULT'],
            [
                'name' => 'Alur Pengaduan Default',
                'description' => 'Masyarakat → Diskominfo → OPD terkait → Petugas → Selesai',
                'category_id' => null,
                'sub_category_id' => null,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        $nodes = [
            'START' => [
                'name' => 'Masyarakat',
                'opd_id' => null,
                'role_name' => UserRole::MASYARAKAT->value,
                'sequence' => 1,
                'is_start' => true,
                'is_end' => false,
                'can_forward' => false,
                'can_assign' => false,
                'can_return' => false,
                'can_reject' => false,
                'can_approve' => false,
                'can_close' => false,
                'can_edit' => true,
            ],
            'VERIFY' => [
                'name' => 'Diskominfo',
                'opd_id' => $diskominfo->id,
                'role_name' => UserRole::ADMIN_OPD->value,
                'sequence' => 2,
                'is_start' => false,
                'is_end' => false,
                'can_forward' => true,
                'can_assign' => false,
                'can_return' => true,
                'can_reject' => true,
                'can_approve' => false,
                'can_close' => false,
                'can_edit' => false,
            ],
            'OPD' => [
                'name' => 'OPD Terkait',
                'opd_id' => null,
                'role_name' => UserRole::ADMIN_OPD->value,
                'sequence' => 3,
                'is_start' => false,
                'is_end' => false,
                'can_forward' => false,
                'can_assign' => true,
                'can_return' => true,
                'can_reject' => true,
                'can_approve' => true,
                'can_close' => false,
                'can_edit' => false,
            ],
            'PETUGAS' => [
                'name' => 'Petugas',
                'opd_id' => null,
                'role_name' => UserRole::PETUGAS->value,
                'sequence' => 4,
                'is_start' => false,
                'is_end' => false,
                'can_forward' => false,
                'can_assign' => false,
                'can_return' => true,
                'can_reject' => false,
                'can_approve' => true,
                'can_close' => false,
                'can_edit' => false,
            ],
            'DONE' => [
                'name' => 'Selesai',
                'opd_id' => null,
                'role_name' => null,
                'sequence' => 5,
                'is_start' => false,
                'is_end' => true,
                'can_forward' => false,
                'can_assign' => false,
                'can_return' => false,
                'can_reject' => false,
                'can_approve' => false,
                'can_close' => true,
                'can_edit' => false,
            ],
        ];

        $nodeModels = [];

        foreach ($nodes as $code => $attrs) {
            $nodeModels[$code] = WorkflowNode::query()->firstOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'code' => $code,
                ],
                array_merge([
                    'can_delegate' => false,
                    'can_escalate' => false,
                    'can_add_note' => true,
                    'can_upload_attachment' => true,
                    'can_change_sla' => false,
                    'can_request_revision' => false,
                    'unit_id' => null,
                    'meta' => null,
                ], $attrs)
            );
        }

        $actions = WorkflowAction::query()
            ->whereIn('code', [
                WorkflowActionCode::VERIFY->value,
                WorkflowActionCode::FORWARD->value,
                WorkflowActionCode::ASSIGN->value,
                WorkflowActionCode::APPROVE->value,
                WorkflowActionCode::CLOSE->value,
            ])
            ->get()
            ->keyBy('code');

        $statuses = ComplaintStatus::query()
            ->whereIn('code', [
                ComplaintStatusCode::VERIFIED->value,
                ComplaintStatusCode::IN_PROGRESS->value,
                ComplaintStatusCode::WAITING_APPROVAL->value,
                ComplaintStatusCode::COMPLETED->value,
                ComplaintStatusCode::CLOSED->value,
            ])
            ->get()
            ->keyBy('code');

        $transitions = [
            [
                'from' => 'START',
                'to' => 'VERIFY',
                'action' => WorkflowActionCode::VERIFY->value,
                'status' => ComplaintStatusCode::VERIFIED->value,
            ],
            [
                'from' => 'VERIFY',
                'to' => 'OPD',
                'action' => WorkflowActionCode::FORWARD->value,
                'status' => ComplaintStatusCode::IN_PROGRESS->value,
            ],
            [
                'from' => 'OPD',
                'to' => 'PETUGAS',
                'action' => WorkflowActionCode::ASSIGN->value,
                'status' => ComplaintStatusCode::IN_PROGRESS->value,
            ],
            [
                'from' => 'PETUGAS',
                'to' => 'OPD',
                'action' => WorkflowActionCode::APPROVE->value,
                'status' => ComplaintStatusCode::WAITING_APPROVAL->value,
            ],
            [
                'from' => 'OPD',
                'to' => 'DONE',
                'action' => WorkflowActionCode::APPROVE->value,
                'status' => ComplaintStatusCode::COMPLETED->value,
            ],
            [
                'from' => 'DONE',
                'to' => 'DONE',
                'action' => WorkflowActionCode::CLOSE->value,
                'status' => ComplaintStatusCode::CLOSED->value,
            ],
        ];

        foreach ($transitions as $transition) {
            WorkflowTransition::query()->firstOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'from_node_id' => $nodeModels[$transition['from']]->id,
                    'to_node_id' => $nodeModels[$transition['to']]->id,
                    'action_id' => $actions[$transition['action']]->id,
                ],
                [
                    'target_status_id' => $statuses[$transition['status']]->id,
                    'conditions' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
