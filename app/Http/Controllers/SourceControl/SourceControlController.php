<?php

namespace App\Http\Controllers\SourceControl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SourceControlController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('source-control/Index');
    }
}
