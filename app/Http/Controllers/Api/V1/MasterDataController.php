<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\ComplaintStatus;
use App\Domain\Complaint\Models\Priority;
use App\Domain\Organization\Models\Opd;
use App\Domain\Region\Models\District;
use App\Domain\Region\Models\Village;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => ComplaintCategory::query()
                ->with(['subCategories' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function priorities(): JsonResponse
    {
        return response()->json([
            'data' => Priority::query()
                ->where('is_active', true)
                ->orderBy('level')
                ->get(),
        ]);
    }

    public function statuses(): JsonResponse
    {
        return response()->json([
            'data' => ComplaintStatus::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function opds(): JsonResponse
    {
        return response()->json([
            'data' => Opd::query()
                ->with(['units' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function districts(Request $request): JsonResponse
    {
        return response()->json([
            'data' => District::query()
                ->when($request->query('regency_id'), fn ($query, string $regencyId) => $query->where('regency_id', $regencyId))
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function villages(Request $request): JsonResponse
    {
        return response()->json([
            'data' => Village::query()
                ->when($request->query('district_id'), fn ($query, string $districtId) => $query->where('district_id', $districtId))
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function regions(Request $request): JsonResponse
    {
        return response()->json([
            'districts' => $this->districts($request)->getData(true)['data'],
            'villages' => $this->villages($request)->getData(true)['data'],
        ]);
    }
}
