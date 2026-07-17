<?php

namespace App\Http\Controllers\Web;

use App\Domain\Configuration\Models\RegionalSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $settings = Schema::hasTable('regional_settings')
            ? RegionalSetting::query()->first()
            : null;

        return view('landing.index', [
            'settings' => $settings,
        ]);
    }
}
