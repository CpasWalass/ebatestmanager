<?php

namespace App\Imports;

use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader;

class ProjectExcelImport
{
    protected int $projectId;

    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Known fields with predefined type, options and colors.
     * These are detected by slug match during import.
     */
    protected static function knownFields(): array
    {
        return [
            'etat_test' => [
                'label' => 'ETAT DE TEST',
                'type'  => 'select',
                'options' => ['À faire', 'En cours', 'Bloqué', 'Terminé'],
                'option_colors' => [
                    'À faire'  => '#6b7280',
                    'En cours' => '#f97316',
                    'Bloqué'   => '#ef4444',
                    'Terminé'  => '#22c55e',
                ],
            ],
            'status' => [
                'label' => 'STATUS',
                'type'  => 'select',
                'options' => ['Validé', 'Non validé', 'Sous réserve', 'Optimisation'],
                'option_colors' => [
                    'Validé'       => '#22c55e',
                    'Non validé'   => '#ef4444',
                    'Sous réserve' => '#a855f7',
                    'Optimisation' => '#f97316',
                ],
            ],
            'nature' => [
                'label' => 'NATURE',
                'type'  => 'select',
                'options' => [
                    'Erreurs Fonctionnelles',
                    'Erreurs de Validation / Saisie',
                    'Erreurs d\'Interface (UI/UX)',
                    'Erreurs Techniques',
                    'Erreurs de Performance',
                    'Erreurs de Sécurité',
                    'Erreurs de Données',
                    'Erreurs d\'Intégration',
                    'Erreurs de Compatibilité',
                    'Erreurs de Workflow / Navigation',
                ],
                'option_colors' => [
                    'Erreurs Fonctionnelles'           => '#ef4444',
                    'Erreurs de Validation / Saisie'   => '#ef4444',
                    'Erreurs d\'Interface (UI/UX)'      => '#ef4444',
                    'Erreurs Techniques'               => '#ef4444',
                    'Erreurs de Performance'           => '#ef4444',
                    'Erreurs de Sécurité'              => '#ef4444',
                    'Erreurs de Données'               => '#ef4444',
                    'Erreurs d\'Intégration'            => '#ef4444',
                    'Erreurs de Compatibilité'         => '#ef4444',
                    'Erreurs de Workflow / Navigation' => '#ef4444',
                ],
            ],
            // Common aliases that may appear in Excel files
            'etat'   => 'etat_test',
            'statut' => 'status',
        ];
    }

    public function import(string $filePath): array
    {
        $reader = new Reader();
        $reader->open($filePath);

        $sheetsImported = 0;
        $rowsImported = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            if (method_exists($sheet, 'isVisible') && !$sheet->isVisible()) {
                continue; // Skip hidden sheets
            }
            
            $sheetName = $sheet->getName();
            
            // Collect rows to analyze
            $rows = [];
            $iterator = $sheet->getRowIterator();
            $rowCount = 0;
            
            foreach ($iterator as $row) {
                $rows[] = $row->toArray();
                $rowCount++;
                if ($rowCount > 10000) break; // Safeguard limit per sheet in memory
            }
            
            if (empty($rows)) {
                continue;
            }

            // Detect header row heuristically
            $headerRowIndex = $this->detectHeaderRow($rows);
            
            if ($headerRowIndex === -1) {
                // No headers detected, skip this sheet
                continue;
            }
            
            $headerCells = $rows[$headerRowIndex];
            $fields = [];
            $fieldMap = []; // original col index -> slug
            
            foreach ($headerCells as $colIndex => $cellValue) {
                if ($cellValue instanceof \DateTimeInterface) {
                    $cellValue = $cellValue->format('Y-m-d H:i:s');
                }
                
                $label = trim((string) $cellValue);
                if (empty($label)) continue;
                
                $slug = Str::slug($label, '_');
                
                // Ensure uniqueness of slugs
                $originalSlug = $slug;
                $counter = 1;
                while (collect($fields)->contains('name', $slug)) {
                    $slug = $originalSlug . '_' . $counter;
                    $counter++;
                }
                
                // Check if this slug matches a known field (with alias resolution)
                $knownFields = self::knownFields();
                $resolvedSlug = $slug;
                if (isset($knownFields[$slug]) && is_string($knownFields[$slug])) {
                    $resolvedSlug = $knownFields[$slug]; // resolve alias
                }
                
                if (isset($knownFields[$resolvedSlug]) && is_array($knownFields[$resolvedSlug])) {
                    $known = $knownFields[$resolvedSlug];
                    $fields[] = [
                        'name'          => $resolvedSlug,
                        'label'         => mb_strtoupper($label),
                        'type'          => $known['type'],
                        'required'      => false,
                        'options'       => $known['options'],
                        'option_colors' => $known['option_colors'],
                    ];
                    $fieldMap[$colIndex] = $resolvedSlug;
                } else {
                    // Auto-detect type
                    $type = 'text';
                    if (Str::length($label) > 30 || Str::contains(mb_strtolower($label), ['description', 'resultat', 'scénario', 'scenario', 'attendu', 'obtenu', 'commentaire'])) {
                        $type = 'textarea';
                    }
                    
                    $fields[] = [
                        'name'     => $slug,
                        'label'    => $label,
                        'type'     => $type,
                        'required' => false,
                    ];
                    
                    $fieldMap[$colIndex] = $slug;
                }
            }
            
            if (empty($fields)) {
                continue;
            }

            // Create new template
            $template = TestCaseTemplate::create([
                'project_id' => $this->projectId,
                'name' => $sheetName,
                'fields' => $fields,
                'links' => [],
            ]);
            
            $sheetsImported++;
            
            // Read data rows
            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $cells = $rows[$i];
                
                // Check if row has data
                $hasData = false;
                foreach ($fieldMap as $colIndex => $slug) {
                    if (!empty($cells[$colIndex])) {
                        $hasData = true;
                        break;
                    }
                }
                
                if (!$hasData) continue;
                
                $data = [];
                foreach ($fields as $field) {
                    $data[$field['name']] = '';
                }
                
                foreach ($fieldMap as $colIndex => $slug) {
                    $val = $cells[$colIndex] ?? '';
                    if ($val instanceof \DateTimeInterface) {
                        $val = $val->format('Y-m-d H:i:s');
                    }
                    $data[$slug] = trim((string) $val);
                }
                
                TestCase::create([
                    'project_id' => $this->projectId,
                    'template_id' => $template->id,
                    'data' => $data,
                ]);
                
                $rowsImported++;
            }
        }

        $reader->close();

        return [
            'sheets' => $sheetsImported,
            'rows' => $rowsImported,
        ];
    }
    
    private function detectHeaderRow(array $rows): int
    {
        $bestScore = 0;
        $bestIndex = -1;
        
        $keywords = ['cas', 'test', 'statut', 'status', 'description', 'resultat', 'attendu', 'obtenu', 'etat', 'priorite', 'scenario', 'etape'];
        
        // Scan up to the first 20 rows
        $limit = min(20, count($rows));
        
        for ($i = 0; $i < $limit; $i++) {
            $score = 0;
            $cells = $rows[$i];
            
            foreach ($cells as $cell) {
                if ($cell instanceof \DateTimeInterface) continue; // dates are rarely headers
                
                $val = trim((string) $cell);
                if (!empty($val)) {
                    $score += 1; // +1 for density
                    
                    $normalized = mb_strtolower($val);
                    $normalized = strtr(
                        utf8_decode($normalized),
                        utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
                    );
                    
                    foreach ($keywords as $kw) {
                        if (str_contains($normalized, $kw)) {
                            $score += 3; // +3 for keyword match
                            break;
                        }
                    }
                }
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIndex = $i;
            }
        }
        
        // Require at least a density of 2 columns or 1 keyword match
        if ($bestScore >= 2) {
            return $bestIndex;
        }
        
        return -1;
    }
}
