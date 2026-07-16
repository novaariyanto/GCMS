<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Complaint\Models\Complaint;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ComplaintResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $complaints = Complaint::query()
            ->with(['category', 'priority', 'status'])
            ->where(function ($query) use ($data): void {
                $query->where('ticket_number', 'like', '%'.$data['q'].'%')
                    ->orWhere('title', 'like', '%'.$data['q'].'%');
            })
            ->latest()
            ->limit(20)
            ->get();

        return ComplaintResource::collection($complaints);
    }
}
