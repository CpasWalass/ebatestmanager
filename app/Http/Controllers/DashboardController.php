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
        $activeProjects = Project::count();
        $uatProjects = Project::where('status', 'en_cours')->count();
        $totalTemplates = \App\Models\TestCaseTemplate::count();
        
        $cases = TestCase::all(['data']);
        $totalTestCases = $cases->count();
        $validCases = 0;
        
        foreach ($cases as $case) {
            $status = strtolower($case->data['status'] ?? $case->data['etat_test'] ?? '');
            if (in_array($status, ['validé', 'terminé', 'valide', 'termine'])) {
                $validCases++;
            }
        }
        
        $validationRate = $totalTestCases > 0 ? round(($validCases / $totalTestCases) * 100) : 0;

        return view('dashboard', [
            'activeProjects' => $activeProjects,
            'uatProjects' => $uatProjects,
            'totalTemplates' => $totalTemplates,
            'validationRate' => $validationRate,
        ]);
    }
}
