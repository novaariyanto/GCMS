<?php

namespace Database\Seeders;

use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\ComplaintStatus;
use App\Domain\Complaint\Models\ComplaintSubCategory;
use App\Domain\Complaint\Models\Priority;
use App\Domain\Configuration\Models\AttachmentType;
use App\Domain\Configuration\Models\WorkingHour;
use App\Domain\Notification\Models\NotificationMedia;
use App\Domain\Organization\Models\Opd;
use App\Domain\Organization\Models\Unit;
use App\Domain\Workflow\Enums\WorkflowActionCode;
use App\Domain\Workflow\Models\WorkflowAction;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedStatuses();
        $this->seedPriorities();
        $this->seedWorkflowActions();
        $this->seedAttachmentTypes();
        $this->seedNotificationMedia();
        $this->seedWorkingHours();
        $opds = $this->seedOpds();
        $this->seedUnits($opds);
        $this->seedCategories($opds);
    }

    private function seedStatuses(): void
    {
        $sortOrder = 1;

        foreach (ComplaintStatusCode::cases() as $status) {
            ComplaintStatus::query()->firstOrCreate(
                ['code' => $status->value],
                [
                    'name' => $status->label(),
                    'color' => $status->color(),
                    'is_final' => $status->isFinal(),
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    private function seedPriorities(): void
    {
        $priorities = [
            ['code' => 'LOW', 'name' => 'Rendah', 'color' => '#198754', 'level' => 1, 'sla_hours' => 168],
            ['code' => 'MEDIUM', 'name' => 'Sedang', 'color' => '#0d6efd', 'level' => 2, 'sla_hours' => 72],
            ['code' => 'HIGH', 'name' => 'Tinggi', 'color' => '#fd7e14', 'level' => 3, 'sla_hours' => 48],
            ['code' => 'CRITICAL', 'name' => 'Kritis', 'color' => '#dc3545', 'level' => 4, 'sla_hours' => 24],
        ];

        foreach ($priorities as $priority) {
            Priority::query()->firstOrCreate(
                ['code' => $priority['code']],
                array_merge($priority, ['is_active' => true])
            );
        }
    }

    private function seedWorkflowActions(): void
    {
        $sortOrder = 1;

        foreach (WorkflowActionCode::cases() as $action) {
            WorkflowAction::query()->firstOrCreate(
                ['code' => $action->value],
                [
                    'name' => $action->name,
                    'label' => $action->label(),
                    'color' => $action->color(),
                    'icon' => null,
                    'requires_remark' => $action->requiresRemark(),
                    'requires_attachment' => false,
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    private function seedAttachmentTypes(): void
    {
        $types = [
            [
                'code' => 'IMAGE',
                'name' => 'Gambar',
                'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                'max_size_kb' => 5120,
            ],
            [
                'code' => 'DOCUMENT',
                'name' => 'Dokumen',
                'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                'max_size_kb' => 10240,
            ],
            [
                'code' => 'VIDEO',
                'name' => 'Video',
                'allowed_extensions' => ['mp4', 'mov', 'avi'],
                'max_size_kb' => 51200,
            ],
            [
                'code' => 'AUDIO',
                'name' => 'Audio',
                'allowed_extensions' => ['mp3', 'wav', 'm4a'],
                'max_size_kb' => 10240,
            ],
        ];

        foreach ($types as $type) {
            AttachmentType::query()->firstOrCreate(
                ['code' => $type['code']],
                array_merge($type, ['is_active' => true])
            );
        }
    }

    private function seedNotificationMedia(): void
    {
        $media = [
            ['code' => 'EMAIL', 'name' => 'Email', 'config' => ['driver' => 'smtp']],
            ['code' => 'WHATSAPP', 'name' => 'WhatsApp', 'config' => ['driver' => 'whatsapp']],
            ['code' => 'SMS', 'name' => 'SMS', 'config' => ['driver' => 'sms']],
            ['code' => 'IN_APP', 'name' => 'In-App', 'config' => ['driver' => 'database']],
        ];

        foreach ($media as $item) {
            NotificationMedia::query()->firstOrCreate(
                ['code' => $item['code']],
                array_merge($item, ['is_active' => true])
            );
        }
    }

    private function seedWorkingHours(): void
    {
        // 1=Monday … 5=Friday, 08:00–16:00
        foreach ([1, 2, 3, 4, 5] as $dayOfWeek) {
            WorkingHour::query()->firstOrCreate(
                [
                    'day_of_week' => $dayOfWeek,
                    'start_time' => '08:00:00',
                ],
                [
                    'end_time' => '16:00:00',
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * @return array<string, Opd>
     */
    private function seedOpds(): array
    {
        $items = [
            [
                'code' => 'DISKOMINFO',
                'name' => 'Dinas Komunikasi dan Informatika',
                'short_name' => 'Diskominfo',
                'description' => 'OPD pengelola pengaduan dan verifikasi awal.',
                'email' => 'diskominfo@kabupatencontoh.go.id',
            ],
            [
                'code' => 'DINKES',
                'name' => 'Dinas Kesehatan',
                'short_name' => 'Dinkes',
                'description' => 'OPD terkait kesehatan masyarakat.',
                'email' => 'dinkes@kabupatencontoh.go.id',
            ],
            [
                'code' => 'PUPR',
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'short_name' => 'Dinas PUPR',
                'description' => 'OPD terkait infrastruktur dan penataan ruang.',
                'email' => 'pupr@kabupatencontoh.go.id',
            ],
        ];

        $opds = [];

        foreach ($items as $item) {
            $opds[$item['code']] = Opd::query()->firstOrCreate(
                ['code' => $item['code']],
                array_merge($item, [
                    'phone' => null,
                    'address' => null,
                    'is_active' => true,
                ])
            );
        }

        return $opds;
    }

    /**
     * @param  array<string, Opd>  $opds
     */
    private function seedUnits(array $opds): void
    {
        $units = [
            ['opd' => 'DISKOMINFO', 'code' => 'PPID', 'name' => 'Unit PPID & Pengaduan'],
            ['opd' => 'DISKOMINFO', 'code' => 'IT', 'name' => 'Unit Infrastruktur TI'],
            ['opd' => 'DINKES', 'code' => 'YANKES', 'name' => 'Unit Pelayanan Kesehatan'],
            ['opd' => 'PUPR', 'code' => 'BINA_MARGA', 'name' => 'Unit Bina Marga'],
        ];

        foreach ($units as $unit) {
            Unit::query()->firstOrCreate(
                [
                    'opd_id' => $opds[$unit['opd']]->id,
                    'code' => $unit['code'],
                ],
                [
                    'name' => $unit['name'],
                    'description' => null,
                    'division_id' => null,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * @param  array<string, Opd>  $opds
     */
    private function seedCategories(array $opds): void
    {
        $infrastruktur = ComplaintCategory::query()->firstOrCreate(
            ['code' => 'INFRASTRUKTUR'],
            [
                'name' => 'Infrastruktur',
                'description' => 'Pengaduan terkait jalan, jembatan, dan fasilitas umum.',
                'icon' => 'road',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $kesehatan = ComplaintCategory::query()->firstOrCreate(
            ['code' => 'KESEHATAN'],
            [
                'name' => 'Kesehatan',
                'description' => 'Pengaduan terkait layanan kesehatan masyarakat.',
                'icon' => 'heart-pulse',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $pelayanan = ComplaintCategory::query()->firstOrCreate(
            ['code' => 'PELAYANAN'],
            [
                'name' => 'Pelayanan Publik',
                'description' => 'Pengaduan terkait pelayanan administrasi dan informasi publik.',
                'icon' => 'building',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $subCategories = [
            [
                'category' => $infrastruktur,
                'code' => 'JALAN_RUSAK',
                'name' => 'Jalan Rusak',
                'default_opd' => 'PUPR',
                'sort_order' => 1,
            ],
            [
                'category' => $infrastruktur,
                'code' => 'DRAINASE',
                'name' => 'Drainase / Genangan',
                'default_opd' => 'PUPR',
                'sort_order' => 2,
            ],
            [
                'category' => $kesehatan,
                'code' => 'LAYANAN_PUSKESMAS',
                'name' => 'Layanan Puskesmas',
                'default_opd' => 'DINKES',
                'sort_order' => 1,
            ],
            [
                'category' => $pelayanan,
                'code' => 'INFORMASI_PUBLIK',
                'name' => 'Informasi Publik',
                'default_opd' => 'DISKOMINFO',
                'sort_order' => 1,
            ],
        ];

        foreach ($subCategories as $sub) {
            ComplaintSubCategory::query()->firstOrCreate(
                [
                    'category_id' => $sub['category']->id,
                    'code' => $sub['code'],
                ],
                [
                    'default_opd_id' => $opds[$sub['default_opd']]->id,
                    'name' => $sub['name'],
                    'description' => null,
                    'sort_order' => $sub['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
