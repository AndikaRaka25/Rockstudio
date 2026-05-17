<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Studio;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::where('is_active', true)->get();
        return view('renter.studio-index', compact('studios'));
    }

    public function show(Studio $studio)
    {
        return view('renter.studio-detail', compact('studio'));
    }
}
