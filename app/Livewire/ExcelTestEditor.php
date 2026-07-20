<?php

namespace App\Livewire;

use App\Imports\TestCaseExcelImport;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class ExcelTestEditor extends Component
{
    use WithFileUploads;

    public Project $project;
    public TestCaseTemplate $template;

    public bool $showColumnModal = false;
    public string $newColumnName = '';
    public string $newColumnType = 'text';

    // Options editor
    public ?string $editingOptionsColumn = null;
    public array $editingOptions = [];
    public bool $showOptionsEditor = false;

    public $excelFile = null;
    public bool $showImportModal = false;
    public ?string $importResult = null;
    public ?string $importError = null;

    // Commit Session
    public bool $showCommitModal = false;
    public string $commitMessage = '';

    // === Validation Client UAT ===
    public bool $showRejectModal = false;
    public ?int $rejectingCaseId = null;
    public string $rejectComment = '';
    public ?string $rejectCaseSummary = null; // résumé du cas pour l'affichage dans le modal

    public function mount(Project $project, TestCaseTemplate $template): void
    {
        $this->project  = $project;
        $this->template = $template;
    }

    /**
     * Force le recalcul de la propriété computed "rows" après l'ajout
     * de cas de test générés par l'IA (composant AiTestCaseGenerator).
     */
    #[On('ai-test-cases-added')]
    public function refreshAfterAiGeneration(): void
    {
        unset($this->rows);
    }

    public function openCommitModal(): void
    {
        $this->commitMessage = '';
        $this->showCommitModal = true;
    }

    public function rules(): array
    {
        return [
            'commitMessage' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'commitMessage.max' => 'Le commentaire ne doit pas dépasser 500 caractères.',
        ];
    }

    public function commitSession(): void
    {
        $this->validate();

        $message = $this->commitMessage ?: 'Aucun commentaire';

        activity()
            ->performedOn($this->project)
            ->causedBy(auth()->user())
            ->log("Session de test soumise ({$this->template->name}) : " . $message);

        $this->showCommitModal = false;
        $this->commitMessage = '';
        session()->flash('success', 'Votre session de tests a été validée avec succès.');
    }

    // =========================================================================
    // Validation Client UAT
    // =========================================================================

    /**
     * Le client valide un cas de test.
     */
    public function validateCase(int $id): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('client')) {
            return;
        }

        $testCase = TestCase::findOrFail($id);
        $testCase->update([
            'client_status'  => 'validated',
            'client_comment' => null,
        ]);

        // Notifier le chef de projet
        $this->notifyChefOnClientAction($testCase, 'validated');

        session()->flash('success', 'Cas de test validé ✅');
    }

    /**
     * Ouvre le modal de rejet pour un cas de test.
     */
    public function openRejectModal(int $id): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('client')) {
            return;
        }

        $testCase = TestCase::findOrFail($id);

        // Construire un résumé lisible du cas pour l'afficher dans le modal
        $data = $testCase->data ?? [];
        $summary = $data['cas_test'] ?? $data['fonctionnalites'] ?? $data['scenarios_test'] ?? ('Cas #' . $id);

        $this->rejectingCaseId  = $id;
        $this->rejectComment    = $testCase->client_comment ?? '';
        $this->rejectCaseSummary = (string) $summary;
        $this->showRejectModal  = true;
    }

    /**
     * Soumet le rejet du client avec commentaire (et éventuellement une image intégrée).
     */
    public function submitRejection(): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('client') || !$this->rejectingCaseId) {
            return;
        }

        $this->validate([
            'rejectComment' => 'required|string|min:5',
        ], [
            'rejectComment.required' => 'Veuillez expliquer la raison du rejet.',
            'rejectComment.min'      => 'Le commentaire doit contenir au moins 5 caractères.',
        ]);

        $testCase = TestCase::findOrFail($this->rejectingCaseId);
        $testCase->update([
            'client_status'  => 'rejected',
            'client_comment' => $this->rejectComment,
        ]);

        // Notifier le chef de projet
        $this->notifyChefOnClientAction($testCase, 'rejected');

        $this->cancelRejection();
        session()->flash('success', 'Rejet soumis avec votre commentaire ❌');
    }

    /**
     * Annule le modal de rejet.
     */
    public function cancelRejection(): void
    {
        $this->showRejectModal   = false;
        $this->rejectingCaseId   = null;
        $this->rejectComment     = '';
        $this->rejectCaseSummary = null;
    }

    /**
     * Envoie une notification interne au(x) chef(s) de projet.
     */
    private function notifyChefOnClientAction(\App\Models\TestCase $testCase, string $action): void
    {
        $client = auth()->user();
        $data   = $testCase->data ?? [];
        $casLabel = $data['cas_test'] ?? $data['fonctionnalites'] ?? ('Cas #' . $testCase->id);

        // Trouver le chef de projet (créateur du projet ou tout utilisateur chef_project du tenant)
        $chefs = \App\Models\User::role('chef_project')->get();

        foreach ($chefs as $chef) {
            if ($action === 'validated') {
                $content = "✅ {$client->name} a validé le cas de test « {$casLabel} » du projet {$this->project->name}.";
            } else {
                $content = "❌ {$client->name} a rejeté le cas de test « {$casLabel} » du projet {$this->project->name}.\n\n"
                    . "Commentaire client :\n{$this->rejectComment}";
            }

            \App\Models\Message::create([
                'sender_id'   => $client->id,
                'receiver_id' => $chef->id,
                'project_id'  => $this->project->id,
                'type'        => 'client_validation',
                'content'     => $content,
            ]);
        }
    }



    #[Computed]
    public function rows()
    {
        return TestCase::where('template_id', $this->template->id)
            ->orderBy('id')
            ->get();
    }

    public function addRow(): void
    {
        $data = [];
        foreach ($this->template->fields as $field) {
            $data[$field['name']] = '';
        }

        if (auth()->check() && auth()->user()->hasRole('client')) {
            $data['_added_by_client'] = true;
        }

        TestCase::create([
            'project_id'  => $this->project->id,
            'template_id' => $this->template->id,
            'type'        => $this->project->type === 'UAT' ? 'uat' : 'iat',
            'data'        => $data,
        ]);
    }

    public function clearColumnData(string $field): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('chef_project')) {
            session()->flash('error', "Vous n'avez pas l'autorisation de vider cette colonne.");
            return;
        }

        $testCases = TestCase::where('template_id', $this->template->id)
            ->where('project_id', $this->project->id)
            ->get();

        foreach ($testCases as $case) {
            $data = $case->data ?? [];
            if (array_key_exists($field, $data)) {
                $data[$field] = '';
                $case->data = $data;
                $case->save();
            }
        }

        unset($this->rows);
        session()->flash('success', "Toutes les données de la colonne ont été vidées avec succès.");
    }

    public function updateCell(int $id, string $field, string $value): void
    {
        $user = auth()->user();

        if ($user && $user->hasRole('client')) {
            $testCase = TestCase::find($id);
            if (!$testCase) return;

            $isAddedByClient = isset($testCase->data['_added_by_client']) && $testCase->data['_added_by_client'];
            $clientEditableKeywords = ['client', 'uat', 'retour_client', 'validation', 'commentaires', 'status', 'etat', 'result', 'nature'];
            
            $isEditable = false;
            if ($isAddedByClient) {
                $isEditable = true;
            } else {
                foreach ($clientEditableKeywords as $keyword) {
                    if (str_contains(strtolower($field), $keyword)) {
                        $isEditable = true;
                        break;
                    }
                }
            }

            if (!$isEditable) {
                // Silently ignore unauthorized edits
                return;
            }
        }

        if ($user && $user->hasRole('tester')) {
            $testerEditableKeywords = ['etat', 'status', 'statut', 'result', 'nature', 'comment'];
            $isEditable = false;
            foreach ($testerEditableKeywords as $keyword) {
                if (str_contains(strtolower($field), $keyword)) {
                    $isEditable = true;
                    break;
                }
            }
            if (!$isEditable) {
                return;
            }
        }

        if ($user && $user->hasRole('developer')) {
            if (!str_contains(strtolower($field), 'retour_dev')) {
                return;
            }
        }

        $testCase = TestCase::find($id);
        if ($testCase) {
            $data          = $testCase->data ?? [];
            $data[$field]  = $value;
            $testCase->data = $data;
            $testCase->save();
        }
    }

    public function deleteRow(int $id): void
    {
        TestCase::destroy($id);
    }

    public function addColumn(): void
    {
        $this->validate([
            'newColumnName' => 'required|string|min:2|max:50',
            'newColumnType' => 'required|in:text,textarea,select,url',
        ]);

        $fields = $this->template->fields;
        $machineName = strtolower(str_replace(' ', '_', $this->newColumnName));
        
        // Add default options for 'select' type if needed
        $options = [];
        if ($this->newColumnType === 'select') {
            $options = ['Option 1', 'Option 2']; // Default options, could be customized later
        }

        $newField = [
            'name' => $machineName,
            'label' => mb_strtoupper($this->newColumnName),
            'type' => $this->newColumnType,
            'required' => false,
        ];
        
        if (!empty($options)) {
            $newField['options'] = $options;
        }

        $fields[] = $newField;

        $this->template->update(['fields' => $fields]);
        $this->newColumnName = '';
        $this->newColumnType = 'text';
        $this->showColumnModal = false;
        
        // Refresh template
        $this->template->refresh();
    }

    public function removeColumn(string $columnName): void
    {
        $fields = $this->template->fields;
        $fields = array_filter($fields, fn($field) => $field['name'] !== $columnName);
        
        $this->template->update(['fields' => array_values($fields)]);
        $this->template->refresh();
    }

    public function updateColumnType(string $columnName, string $newType): void
    {
        $fields = $this->template->fields;
        
        foreach ($fields as &$field) {
            if ($field['name'] === $columnName) {
                $field['type'] = $newType;
                if ($newType === 'select' && empty($field['options'])) {
                    $field['options'] = ['Option 1', 'Option 2'];
                }
                break;
            }
        }
        
        $this->template->update(['fields' => $fields]);
        $this->template->refresh();
    }

    public function openOptionsEditor(string $columnName): void
    {
        $this->showColumnModal = false; // Fermer le modal colonnes en premier
        $this->editingOptionsColumn = $columnName;
        $this->editingOptions = [];
        
        $fields = $this->template->fields;
        foreach ($fields as $field) {
            if ($field['name'] === $columnName) {
                $options = $field['options'] ?? [];
                $colors  = $field['option_colors'] ?? [];
                foreach ($options as $opt) {
                    $this->editingOptions[] = [
                        'value' => $opt,
                        'color' => $colors[$opt] ?? '#6b7280',
                    ];
                }
                break;
            }
        }
        
        if (empty($this->editingOptions)) {
            $this->editingOptions = [
                ['value' => '', 'color' => '#6b7280'],
            ];
        }
        
        $this->showOptionsEditor = true;
    }

    public function addOption(): void
    {
        $this->editingOptions[] = ['value' => '', 'color' => '#6b7280'];
    }

    public function removeOption(int $index): void
    {
        unset($this->editingOptions[$index]);
        $this->editingOptions = array_values($this->editingOptions);
    }

    public function saveOptions(): void
    {
        $fields = $this->template->fields;
        
        foreach ($fields as &$field) {
            if ($field['name'] === $this->editingOptionsColumn) {
                $options = [];
                $colors  = [];
                foreach ($this->editingOptions as $opt) {
                    $val = trim($opt['value']);
                    if ($val !== '') {
                        $options[] = $val;
                        $colors[$val] = $opt['color'];
                    }
                }
                $field['options'] = $options;
                $field['option_colors'] = $colors;
                break;
            }
        }
        
        $this->template->update(['fields' => $fields]);
        $this->template->refresh();
        $this->showOptionsEditor = false;
        $this->editingOptionsColumn = null;
        $this->editingOptions = [];
    }

    public function closeOptionsEditor(): void
    {
        $this->showOptionsEditor = false;
        $this->editingOptionsColumn = null;
        $this->editingOptions = [];
    }

    #[Computed]
    public function allLinks()
    {
        $projectLinks = $this->project->links ?? [];
        $templateLinks = $this->template->links ?? [];
        
        return array_merge($projectLinks, $templateLinks);
    }

    public function importExcel(): void
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excelFile.required' => 'Veuillez sélectionner un fichier.',
            'excelFile.mimes'    => 'Le fichier doit être un fichier Excel (.xlsx, .xls) ou CSV.',
            'excelFile.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $path = $this->excelFile->getRealPath();
            $importer = new TestCaseExcelImport($this->template, $this->project->id);
            $count = $importer->import($path);

            $this->importResult = "$count ligne(s) importée(s) avec succès !";
            $this->importError = null;
            $this->excelFile = null;
            $this->template->refresh();
        } catch (\Exception $e) {
            $this->importError = 'Erreur : ' . $e->getMessage();
            $this->importResult = null;
        }
    }

    public function render()
    {
        return view('livewire.excel-test-editor');
    }
}
