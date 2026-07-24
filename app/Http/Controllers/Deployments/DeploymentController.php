<?php

namespace App\Http\Controllers\Deployments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeploymentController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('deployments/Index');
    }
}
