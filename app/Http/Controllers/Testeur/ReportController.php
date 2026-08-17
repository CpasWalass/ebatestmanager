<?php

namespace App\Http\Controllers\Testeur;

use App\Http\Controllers\Controller;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ReportController extends Controller
{
    public function generateGlobalReport(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $user = auth()->user();

        // Projets et templates assignés
        $assignedProjectIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();

        $assignedTemplateIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('template_id')
            ->pluck('template_id')
            ->toArray();

        $assignedTemplateProjectIds = TestCaseTemplate::whereIn('id', $assignedTemplateIds)
            ->pluck('project_id')
            ->toArray();

        $allAssignedProjectIds = array_unique(array_merge($assignedProjectIds, $assignedTemplateProjectIds));

        // 1. Statistiques globales (en tests) - exclure projets terminés/archivés
        $assignedCases = TestCase::whereHas('project', function ($q) {
            $q->whereNotIn('status', ['completed', 'archived']);
        })
            ->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)
                    ->orWhereIn('template_id', $assignedTemplateIds);
            })
            ->get();

        $stats = TestCase::statsFor($assignedCases);

        $totalAssigned = $stats['total'];
        $totalExecuted = $stats['executed'];

        $successCount = $stats['valide'];
        $failureCount = $stats['non_valide'];
        $reserveCount = $stats['sous_reserve'];
        $optimCount = $stats['optimisation'];

        $successRate = $totalExecuted > 0 ? round(($successCount / $totalExecuted) * 100, 1) : 0;

        // 2. Répartition par Template (Cas de Test) - exclure projets terminés/archivés
        $templates = TestCaseTemplate::whereHas('project', function ($q) {
            $q->whereNotIn('status', ['completed', 'archived']);
        })
            ->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)
                    ->orWhereIn('id', $assignedTemplateIds);
            })
            ->with(['project'])
            ->get();

        $templatesProgress = [];
        foreach ($templates as $template) {
            $templateCases = TestCase::where('template_id', $template->id)->get();
            $testCount = $templateCases->count();
            if ($testCount > 0) {
                $templateStats = TestCase::statsFor($templateCases);
                $validCount = $templateStats['valide'];

                $templatesProgress[] = [
                    'project' => $template->project->name ?? '—',
                    'name' => $template->name,
                    'assigned' => $testCount,
                    'validated' => $validCount,
                    'percent' => round(($validCount / $testCount) * 100),
                ];
            }
        }

        $filename = 'rapport-global-testeur-'.\Str::slug($user->name).'-'.date('Ymd');

        if ($format === 'word') {
            return $this->generateWord($user, $totalAssigned, $totalExecuted, $successCount, $failureCount, $reserveCount, $optimCount, $successRate, $templatesProgress, $filename);
        }

        // PDF (par défaut)
        $pdf = Pdf::loadView('reports.testeur-global-pdf', compact(
            'user',
            'totalAssigned',
            'totalExecuted',
            'successCount',
            'failureCount',
            'reserveCount',
            'optimCount',
            'successRate',
            'templatesProgress'
        ))->setPaper('a4', 'portrait')->setOption('defaultFont', 'sans-serif');

        return $pdf->download($filename.'.pdf');
    }

    private function generateWord($user, $totalAssigned, $totalExecuted, $successCount, $failureCount, $reserveCount, $optimCount, $successRate, $templatesProgress, $filename)
    {
        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection();

        // Styles
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20, 'color' => 'CC0000'], ['spaceAfter' => 200]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'color' => 'CC0000'], ['spaceBefore' => 300, 'spaceAfter' => 100]);

        // En-tête
        $header = $section->addHeader();
        $header->addText('e-Business Afrique — EbaTestManager', ['size' => 9, 'color' => '888888']);

        // Titre principal
        $section->addTitle('STATISTIQUES TESTEUR', 1);
        $section->addText($user->name.' — '.$user->email, ['size' => 11, 'color' => '555555']);
        $section->addText('Rapport généré le '.now()->format('d/m/Y à H:i'), ['size' => 10, 'color' => '888888', 'italic' => true]);
        $section->addTextBreak(1);

        // Informations générales
        $section->addTitle('Informations Générales', 2);
        $infoTable = $section->addTable(['borderColor' => 'DDDDDD', 'borderSize' => 6, 'cellMargin' => 80]);
        $labelStyle = ['bold' => true, 'color' => '888888', 'size' => 10];
        $valueStyle = ['bold' => true, 'size' => 11];

        $rows = [
            ['Tests assignés', $totalAssigned],
            ['Tests exécutés', $totalExecuted],
            ['Taux de validation globale', $successRate.'%'],
            ['Date du rapport', now()->format('d/m/Y')],
        ];
        foreach ($rows as [$label, $value]) {
            $row = $infoTable->addRow();
            $row->addCell(3000)->addText($label, $labelStyle);
            $row->addCell(5000)->addText((string) $value, $valueStyle);
        }
        $section->addTextBreak(1);

        // Statistiques d'exécution
        $section->addTitle("Statistiques d'Exécution", 2);
        $statsTable = $section->addTable(['borderColor' => 'DDDDDD', 'borderSize' => 6, 'cellMargin' => 80]);
        $headerRow = $statsTable->addRow();
        $headerRow->addCell(3000)->addText('Statut', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['bgColor' => 'CC0000']);
        $headerRow->addCell(2000)->addText('Nombre', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['bgColor' => 'CC0000']);

        $statRows = [
            ['Validés', $successCount, '16a34a'],
            ['Échecs', $failureCount, 'CC0000'],
            ['Sous réserve', $reserveCount, 'f59e0b'],
            ['Optimisation', $optimCount, '3b82f6'],
        ];
        foreach ($statRows as [$label, $count, $color]) {
            $row = $statsTable->addRow();
            $row->addCell(3000)->addText($label, ['color' => $color, 'bold' => true]);
            $row->addCell(2000)->addText((string) $count, ['color' => $color, 'bold' => true]);
        }
        $section->addTextBreak(1);

        // Avancement par Cas de Test
        if (! empty($templatesProgress)) {
            $section->addTitle('Avancement par Cas de Test', 2);
            $tplTable = $section->addTable(['borderColor' => 'DDDDDD', 'borderSize' => 6, 'cellMargin' => 80]);
            $hRow = $tplTable->addRow();
            foreach (['Projet', 'Cas de test', 'Tests assignés', 'Tests validés', 'Progression'] as $th) {
                $hRow->addCell(null)->addText($th, ['bold' => true, 'color' => 'FFFFFF', 'size' => 9], ['bgColor' => 'CC0000']);
            }
            foreach ($templatesProgress as $p) {
                $row = $tplTable->addRow();
                $row->addCell(null)->addText($p['project'], ['size' => 10]);
                $row->addCell(null)->addText($p['name'], ['size' => 10]);
                $row->addCell(null)->addText((string) $p['assigned'], ['size' => 10]);
                $row->addCell(null)->addText((string) $p['validated'], ['size' => 10]);
                $row->addCell(null)->addText($p['percent'].'%', ['size' => 10, 'bold' => true]);
            }
        }

        // Pied de page
        $footer = $section->addFooter();
        $footer->addText('Généré le '.now()->format('d/m/Y à H:i').' — EbaTestManager by e-Business Afrique', ['size' => 9, 'color' => '888888']);

        $tempPath = storage_path('app/temp_'.$filename.'.docx');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return response()->download($tempPath, $filename.'.docx')->deleteFileAfterSend(true);
    }
}
