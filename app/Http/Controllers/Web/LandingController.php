<?php

namespace App\Http\Controllers\Web;

use App\Domain\Complaint\Models\Complaint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $hasComplaintTable = Schema::hasTable('complaints');

        return view('landing.index', [
            'totalComplaints' => $hasComplaintTable ? Complaint::query()->count() : 0,
            'resolvedComplaints' => $hasComplaintTable ? Complaint::query()->whereNotNull('resolved_at')->count() : 0,
        ]);
    }
}
