<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Get KPI data
        $activeProjects = Project::count();
        $uatProjects = Project::where('status', 'in_progress')->count();
        $totalTemplates = \App\Models\TestCaseTemplate::count();
        $validationRate = 78; // Calculate from executions

        return view('dashboard', [
            'activeProjects' => $activeProjects,
            'uatProjects' => $uatProjects,
            'totalTemplates' => $totalTemplates,
            'validationRate' => $validationRate,
        ]);
    }
}
