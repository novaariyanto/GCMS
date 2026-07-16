<?php

namespace App\Http\Controllers\Web;

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Application\Complaint\Services\ComplaintService;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\Priority;
use App\Domain\Region\Models\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ComplaintWebController extends Controller
{
    public function __construct(private readonly ComplaintService $complaintService) {}

    public function index(Request $request): View
    {
        $complaints = Complaint::query()
            ->with(['category', 'priority', 'status'])
            ->where('reporter_id', $request->user()->id)
            ->when($request->query('q'), function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('portal.complaints.index', compact('complaints'));
    }

    public function create(): View
    {
        return view('portal.complaints.create', [
            'categories' => ComplaintCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'priorities' => Priority::query()->where('is_active', true)->orderBy('level')->get(),
            'districts' => District::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'uuid', 'exists:complaint_categories,id'],
            'priority_id' => ['nullable', 'uuid', 'exists:priorities,id'],
            'district_id' => ['nullable', 'uuid', 'exists:districts,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'reporter_phone' => ['nullable', 'string', 'max:30'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ]);

        $priority = $this->resolvePriority($data['priority_id'] ?? null);
        $complaint = $this->complaintService->create(CreateComplaintData::fromArray([
            ...$data,
            'reporter_id' => $request->user()->id,
            'priority_id' => $priority->id,
            'reporter_name' => $request->user()->name,
            'reporter_email' => $request->user()->email,
            'reporter_phone' => $data['reporter_phone'] ?? $request->user()->phone,
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
        ]));

        return redirect()
            ->route('portal.complaints.show', $complaint)
            ->with('success', 'Pengaduan berhasil dikirim. Nomor tiket: '.$complaint->ticket_number);
    }

    public function show(Request $request, Complaint $complaint): View
    {
        abort_unless($complaint->reporter_id === $request->user()->id, 403);

        return view('portal.complaints.show', [
            'complaint' => $complaint->load(['category', 'priority', 'status', 'currentOpd', 'timelines.user', 'workflowHistories.action']),
        ]);
    }

    public function track(string $ticket): View
    {
        $complaint = Complaint::query()
            ->with(['category', 'priority', 'status', 'currentOpd', 'timelines.user'])
            ->where('ticket_number', $ticket)
            ->firstOrFail();

        return view('tracking.show', compact('complaint'));
    }

    private function resolvePriority(?string $priorityId): Priority
    {
        $priority = $priorityId
            ? Priority::query()->find($priorityId)
            : Priority::query()->where('is_active', true)->orderBy('level')->first();

        if (! $priority) {
            throw ValidationException::withMessages([
                'priority_id' => 'Data prioritas belum tersedia.',
            ]);
        }

        return $priority;
    }
}
