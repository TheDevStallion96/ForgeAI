<?php

namespace App\Http\Controllers\ArchitectureStudio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchitectureStudioController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('architecture/Index');
    }

    public function adrs(Request $request): Response
    {
        return Inertia::render('architecture/Adrs');
    }
}
