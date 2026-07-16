<?php

use App\Domain\Workflow\Enums\WorkflowActionCode;

it('provides stable labels and colors for core workflow actions', function (): void {
    expect(WorkflowActionCode::VERIFY->label())->toBe('Verifikasi')
        ->and(WorkflowActionCode::FORWARD->label())->toBe('Teruskan')
        ->and(WorkflowActionCode::ASSIGN->label())->toBe('Tugaskan')
        ->and(WorkflowActionCode::APPROVE->label())->toBe('Setujui')
        ->and(WorkflowActionCode::CLOSE->label())->toBe('Tutup')
        ->and(WorkflowActionCode::VERIFY->color())->toBe('#0dcaf0')
        ->and(WorkflowActionCode::CLOSE->color())->toBe('#212529');
});

it('marks only note-sensitive workflow actions as requiring a remark', function (): void {
    $required = [
        WorkflowActionCode::RETURN,
        WorkflowActionCode::REJECT,
        WorkflowActionCode::REQUEST_REVISION,
        WorkflowActionCode::ESCALATE,
    ];

    foreach (WorkflowActionCode::cases() as $action) {
        expect($action->requiresRemark())->toBe(in_array($action, $required, true));
    }
});
