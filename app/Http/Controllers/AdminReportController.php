<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class AdminReportController extends Controller
{
    public function generate(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $filterPeriod = $request->input('period', 'all');
        $filterProject = $request->input('project', 'all');
        $filterTester = $request->input('tester', 'all');

        $projectsQuery = Project::query();
        if ($filterProject !== 'all') {
            $projectsQuery->where('id', $filterProject);
        }
        if ($filterPeriod !== 'all') {
            $now = now();
            if ($filterPeriod === 'this_month') {
                $projectsQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            } elseif ($filterPeriod === 'last_month') {
                $projectsQuery->whereMonth('created_at', $now->subMonth()->month)->whereYear('created_at', $now->year);
            } elseif ($filterPeriod === 'this_year') {
                $projectsQuery->whereYear('created_at', $now->year);
            }
        }
        $activeProjectsCount = (clone $projectsQuery)->whereNotIn('status', ['completed', 'archived'])->count();
        $uatProjectsCount = (clone $projectsQuery)->where('status', 'in_progress')->count();

        $testCasesQuery = TestCase::whereHas('project', function ($q) {
            $q->whereNotIn('status', ['completed', 'archived']);
        });
        if ($filterProject !== 'all') {
            $testCasesQuery->where('project_id', $filterProject);
        }
        if ($filterTester !== 'all') {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $filterTester)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $filterTester)->whereNotNull('template_id')->pluck('template_id')->toArray();
            $testCasesQuery->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)->orWhereIn('template_id', $assignedTemplateIds);
            });
        }

        $allTestCasesQuery = (clone $testCasesQuery)->get();
        $adminStats = TestCase::statsFor($allTestCasesQuery);
        $totalExecutions = $adminStats['executed'];
        $validExecutions = $adminStats['valide'];
        $validationRate = $totalExecutions > 0 ? round(($validExecutions / $totalExecutions) * 100) : 0;
        $totalTemplates = $adminStats['total'];

        $testersCapacity = User::role('tester')->get()->map(function ($tester) {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('template_id')->pluck('template_id')->toArray();
            $assignedCases = TestCase::whereHas('project', function ($q) {
                $q->whereNotIn('status', ['completed', 'archived']);
            })
                ->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                    $q->whereIn('project_id', $assignedProjectIds)->orWhereIn('template_id', $assignedTemplateIds);
                })
                ->get();
            $testerStats = TestCase::statsFor($assignedCases);
            $total = $testerStats['total'];
            $done = $testerStats['executed'];
            $percent = $total > 0 ? round(($done / $total) * 100) : 100;

            return [
                'name' => $tester->name,
                'total' => $total,
                'done' => $done,
                'percent' => $percent,
                'status' => $percent >= 100 ? 'Disponible' : ($percent > 50 ? 'En cours' : 'Charg&eacute;'),
                'color' => $percent >= 100 ? 'green' : ($percent > 50 ? 'yellow' : 'red'),
            ];
        });

        $filename = 'rapport-admin-filtre-'.date('Ymd');

        if ($format === 'word') {
            return $this->generateWord($activeProjectsCount, $uatProjectsCount, $totalTemplates, $validationRate, $testersCapacity, $filterPeriod, $filterProject, $filterTester, $filename);
        }

        $pdf = Pdf::loadView('reports.admin-global-pdf', compact(
            'activeProjectsCount', 'uatProjectsCount', 'totalTemplates', 'validationRate',
            'testersCapacity', 'filterPeriod', 'filterProject', 'filterTester'
        ))->setPaper('a4', 'portrait')->setOption('defaultFont', 'sans-serif');

        return $pdf->download($filename.'.pdf');
    }

    /**
     * Statistiques détaillées par projet (cas de test + répartition des statuts),
     * exportées en PDF. Reprend exactement les filtres du tableau de bord.
     */
    public function generateStatsPdf(Request $request)
    {
        $type = $request->input('type', 'cases');
        $filterPeriod = $request->input('period', 'all');
        $filterProject = $request->input('project', 'all');
        $filterTester = $request->input('tester', 'all');

        $projectsQuery = Project::query();
        if ($filterProject !== 'all') {
            $projectsQuery->where('id', $filterProject);
        }
        if ($filterPeriod !== 'all') {
            $now = now();
            if ($filterPeriod === 'this_month') {
                $projectsQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            } elseif ($filterPeriod === 'last_month') {
                $projectsQuery->whereMonth('created_at', $now->subMonth()->month)->whereYear('created_at', $now->year);
            } elseif ($filterPeriod === 'this_year') {
                $projectsQuery->whereYear('created_at', $now->year);
            }
        }

        $testCasesQuery = TestCase::whereHas('project', function ($q) {
            $q->whereNotIn('status', ['completed', 'archived']);
        });
        if ($filterProject !== 'all') {
            $testCasesQuery->where('project_id', $filterProject);
        }
        if ($filterTester !== 'all') {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $filterTester)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $filterTester)->whereNotNull('template_id')->pluck('template_id')->toArray();
            $testCasesQuery->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)->orWhereIn('template_id', $assignedTemplateIds);
            });
        }

        $allTestCases = $testCasesQuery->with('project')->get();

        $perProjectStats = $allTestCases->groupBy('project_id')->map(function ($cases) {
            $stats = TestCase::statsFor($cases);
            $project = $cases->first()->project;

            return [
                'name' => $project?->name ?? ('Projet #'.$cases->first()->project_id),
                'total' => $stats['total'],
                'executed' => $stats['executed'],
                'valide' => $stats['valide'],
                'non_valide' => $stats['non_valide'],
                'sous_reserve' => $stats['sous_reserve'],
                'optimisation' => $stats['optimisation'],
                'bloque' => $stats['bloque'],
                'non_executed' => $stats['total'] - $stats['executed'],
                'taux_execution' => $stats['taux_execution'],
            ];
        })->sortByDesc('total')->values();

        $totals = [
            'total' => $perProjectStats->sum('total'),
            'executed' => $perProjectStats->sum('executed'),
            'valide' => $perProjectStats->sum('valide'),
            'non_valide' => $perProjectStats->sum('non_valide'),
            'sous_reserve' => $perProjectStats->sum('sous_reserve'),
            'optimisation' => $perProjectStats->sum('optimisation'),
            'non_executed' => $perProjectStats->sum('non_executed'),
        ];

        $pdf = Pdf::loadView('reports.admin-stats-pdf', compact(
            'perProjectStats', 'totals', 'type', 'filterPeriod', 'filterProject', 'filterTester'
        ))->setPaper('a4', 'landscape')->setOption('defaultFont', 'sans-serif');

        return $pdf->download('statistiques-par-projet-'.date('Ymd').'.pdf');
    }

    private function generateWord($activeProjectsCount, $uatProjectsCount, $totalTemplates, $validationRate, $testersCapacity, $filterPeriod, $filterProject, $filterTester, $filename)
    {
        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection();
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20, 'color' => 'CC0000'], ['spaceAfter' => 200]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'color' => 'CC0000'], ['spaceBefore' => 300, 'spaceAfter' => 100]);

        $header = $section->addHeader();
        $header->addText('e-Business Afrique - EbaTestManager - Rapport Administrateur', ['size' => 9, 'color' => '888888']);

        $section->addTitle('STATISTIQUES & CAPACITE EQUIPE', 1);

        $periodLabels = ['all' => 'Toutes periodes', 'this_month' => 'Ce mois-ci', 'last_month' => 'Le mois dernier', 'this_year' => 'Cette annee'];
        $section->addText('Filtres : '.($periodLabels[$filterPeriod] ?? $filterPeriod).' | Projet : '.($filterProject === 'all' ? 'Tous' : $filterProject).' | Testeur : '.($filterTester === 'all' ? 'Tous' : $filterTester), ['size' => 10, 'italic' => true, 'color' => '888888']);
        $section->addTextBreak(1);

        $section->addTitle('Indicateurs Cles', 2);
        $kpiTable = $section->addTable(['borderColor' => 'DDDDDD', 'borderSize' => 6, 'cellMargin' => 80]);
        foreach ([['Projets Actifs', $activeProjectsCount], ['En Phase Test (UAT)', $uatProjectsCount], ['Tests Assignes', $totalTemplates], ['Taux de Validation', $validationRate.'%']] as [$label, $value]) {
            $row = $kpiTable->addRow();
            $row->addCell(4000)->addText($label, ['bold' => true, 'color' => '555555']);
            $row->addCell(3000)->addText((string) $value, ['bold' => true, 'size' => 13]);
        }
        $section->addTextBreak(1);

        $section->addTitle('Capacite de l\'Equipe (Testeurs)', 2);
        $capTable = $section->addTable(['borderColor' => 'DDDDDD', 'borderSize' => 6, 'cellMargin' => 80]);
        $hRow = $capTable->addRow();
        foreach (['Testeur', 'Tests Assignes', 'Tests Executes', 'Progression', 'Statut'] as $th) {
            $hRow->addCell(null)->addText($th, ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['bgColor' => 'CC0000']);
        }
        foreach ($testersCapacity as $t) {
            $colorMap = ['green' => '16a34a', 'yellow' => 'f59e0b', 'red' => 'CC0000'];
            $c = $colorMap[$t['color']] ?? '333333';
            $row = $capTable->addRow();
            $row->addCell(null)->addText($t['name'], ['bold' => true, 'size' => 10]);
            $row->addCell(null)->addText((string) $t['total'], ['size' => 10]);
            $row->addCell(null)->addText((string) $t['done'], ['size' => 10]);
            $row->addCell(null)->addText($t['percent'].'%', ['bold' => true, 'size' => 10, 'color' => $c]);
            $statusLabel = $t['percent'] >= 100 ? 'DISPONIBLE' : ($t['percent'] > 50 ? 'EN COURS' : 'CHARGE');
            $row->addCell(null)->addText($statusLabel, ['bold' => true, 'size' => 10, 'color' => $c]);
        }

        $footer = $section->addFooter();
        $footer->addText('Genere le '.now()->format('d/m/Y').' - EbaTestManager by e-Business Afrique', ['size' => 9, 'color' => '888888']);

        $tempPath = storage_path('app/temp_'.$filename.'.docx');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return response()->download($tempPath, $filename.'.docx')->deleteFileAfterSend(true);
    }
}
