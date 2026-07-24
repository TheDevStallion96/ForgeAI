<?php

namespace App\Http\Controllers\KnowledgeHub;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KnowledgeHubController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('knowledge/Index');
    }
}
