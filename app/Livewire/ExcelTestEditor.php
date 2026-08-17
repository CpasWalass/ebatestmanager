<?php

namespace App\Livewire;

use App\Imports\TestCaseExcelImport;
use App\Models\Message;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use App\Models\User;
use App\Support\ProjectAccess;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExcelTestEditor extends Component
{
    use WithFileUploads;

    public Project $project;

    public TestCaseTemplate $template;

    public bool $showColumnModal = false;

    public string $newColumnName = '';

    public string $newColumnType = 'text';

    public ?string $editingOptionsColumn = null;

    public array $editingOptions = [];

    public bool $showOptionsEditor = false;

    public $excelFile = null;

    public bool $showImportModal = false;

    public ?string $importResult = null;

    public ?string $importError = null;

    public bool $showCommitModal = false;

    public string $commitMessage = '';

    public bool $showRejectModal = false;

    public ?int $rejectingCaseId = null;

    public string $rejectComment = '';

    public ?string $rejectCaseSummary = null;

    public function mount(Project $project, TestCaseTemplate $template): void
    {
        $this->authorize('view', $project);

        $this->project = $project;
        $this->template = $template;
    }

    /**
     * Un utilisateur peut agir sur les DONNÉES (lignes/cellules) de ce template
     * s'il est chef de projet, développeur du projet, ou explicitement assigné
     * (projet entier ou ce template précisément). Avant cette réécriture, rien
     * ne vérifiait qu'un développeur ou un testeur assigné à un AUTRE projet ne
     * pouvait pas modifier les cas de test de celui-ci simplement en connaissant
     * un ID de test case — seul le NOM du champ était vérifié, jamais le projet.
     */
    private function ensureCanEditData(): void
    {
        $user = auth()->user();

        if ($user->hasRole('chef_project') || ProjectAccess::isDeveloperOnProject($user, $this->project)) {
            return;
        }

        if (ProjectAccess::isAssignedToTemplate($user, $this->template)) {
            return;
        }

        abort(403, "Vous n'êtes pas assigné à ce cas de test.");
    }

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
        $this->ensureCanEditData();
        $this->validate();

        $message = $this->commitMessage ?: 'Aucun commentaire';

        activity()
            ->performedOn($this->project)
            ->causedBy(auth()->user())
            ->log("Session de test soumise ({$this->template->name}) : ".$message);

        $this->showCommitModal = false;
        $this->commitMessage = '';
        session()->flash('success', 'Votre session de tests a été validée avec succès.');
    }

    // =========================================================================
    // Validation Client UAT
    // =========================================================================

    public function validateCase(int $id): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('client')) {
            return;
        }

        $testCase = TestCase::findOrFail($id);
        $this->authorize('validate', $testCase);

        $testCase->update([
            'client_status' => 'validated',
            'client_comment' => null,
            'client_status_by' => auth()->id(),
        ]);

        $this->notifyChefOnClientAction($testCase, 'validated');

        unset($this->rows);

        session()->flash('success', 'Cas de test validé ✅');
    }

    public function resetCase(int $id): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('client')) {
            return;
        }

        $testCase = TestCase::findOrFail($id);
        $this->authorize('validate', $testCase);

        $testCase->update([
            'client_status' => 'pending',
            'client_comment' => null,
            'client_status_by' => auth()->id(),
        ]);

        unset($this->rows);

        session()->flash('success', 'Avis réinitialisé ↺');
    }

    public function openRejectModal(int $id): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('client')) {
            return;
        }

        $testCase = TestCase::findOrFail($id);
        $this->authorize('validate', $testCase);

        $data = $testCase->data ?? [];
        $summary = $data['cas_test'] ?? $data['fonctionnalites'] ?? $data['scenarios_test'] ?? ('Cas #'.$id);

        $this->rejectingCaseId = $id;
        $this->rejectComment = $testCase->client_comment ?? '';
        $this->rejectCaseSummary = (string) $summary;
        $this->showRejectModal = true;
    }

    public function submitRejection(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('client') || ! $this->rejectingCaseId) {
            return;
        }

        $testCase = TestCase::findOrFail($this->rejectingCaseId);
        $this->authorize('validate', $testCase);

        $this->validate([
            'rejectComment' => 'required|string|min:5',
        ], [
            'rejectComment.required' => 'Veuillez expliquer la raison du rejet.',
            'rejectComment.min' => 'Le commentaire doit contenir au moins 5 caractères.',
        ]);

        $testCase->update([
            'client_status' => 'rejected',
            'client_comment' => $this->rejectComment,
            'client_status_by' => auth()->id(),
        ]);

        $this->notifyChefOnClientAction($testCase, 'rejected');

        $this->showRejectModal = false;
        $this->rejectingCaseId = null;
        $this->rejectComment = '';

        unset($this->rows);

        session()->flash('success', 'Cas de test rejeté avec vos commentaires.');
    }

    public function cancelRejection(): void
    {
        $this->showRejectModal = false;
        $this->rejectingCaseId = null;
        $this->rejectComment = '';
        $this->rejectCaseSummary = null;
    }

    private function notifyChefOnClientAction(TestCase $testCase, string $action): void
    {
        $client = auth()->user();
        $data = $testCase->data ?? [];
        $casLabel = $data['cas_test'] ?? $data['fonctionnalites'] ?? ('Cas #'.$testCase->id);

        $chefs = User::role('chef_project')->get();

        foreach ($chefs as $chef) {
            if ($action === 'validated') {
                $content = "✅ {$client->name} a validé le cas de test « {$casLabel} » du projet {$this->project->name}.";
            } else {
                $content = "❌ {$client->name} a rejeté le cas de test « {$casLabel} » du projet {$this->project->name}.\n\n"
                    ."Commentaire client :\n{$this->rejectComment}";
            }

            Message::create([
                'sender_id' => $client->id,
                'receiver_id' => $chef->id,
                'project_id' => $this->project->id,
                'type' => 'client_validation',
                'content' => $content,
            ]);
        }
    }

    #[Computed]
    public function rows()
    {
        return TestCase::where('template_id', $this->template->id)
            ->with('verdictBy', 'progressBy')
            ->orderBy('id')
            ->get();
    }

    public function addRow(): void
    {
        $this->ensureCanEditData();

        $data = [];
        foreach ($this->template->fields as $field) {
            $data[$field['name']] = '';
        }

        if (auth()->user()->hasRole('client')) {
            $data['_added_by_client'] = true;
        }

        TestCase::create([
            'project_id' => $this->project->id,
            'template_id' => $this->template->id,
            // Toujours en minuscules désormais (avant : comparé à 'UAT' en
            // majuscules, ce qui marquait tout nouveau cas comme IAT par erreur
            // dès qu'un projet passait en UAT).
            'type' => $this->project->type === 'uat' ? 'uat' : 'iat',
            'data' => $data,
        ]);

        unset($this->rows);
    }

    /**
     * Met à jour l'axe "progress" (À faire / En cours / Bloqué / Terminé) d'un
     * cas de test. Remplace l'ancien mécanisme où ce statut était un champ
     * libre parmi d'autres (data['etat_test']), avec une simple chaîne de
     * caractères non validée — ici la valeur est vérifiée contre la liste
     * autorisée avant écriture.
     */
    public function updateProgress(int $id, string $value): void
    {
        $this->ensureCanEditData();

        if (! array_key_exists($value, TestCaseTemplate::progressOptions())) {
            return;
        }

        TestCase::where('id', $id)->where('template_id', $this->template->id)->update(['progress' => $value, 'progress_by' => auth()->id()]);
        unset($this->rows);
    }

    /**
     * Met à jour l'axe "verdict" (Validé / Non validé / Sous réserve /
     * Optimisation), désormais une colonne dédiée plutôt qu'un champ libre.
     */
    public function updateVerdict(int $id, ?string $value): void
    {
        $this->ensureCanEditData();

        if ($value !== null && ! array_key_exists($value, TestCaseTemplate::verdictOptions())) {
            return;
        }

        $data = [
            'verdict' => $value,
            'verdict_by' => auth()->id(),
            'verdict_set_at' => now(),
        ];

        if ($value) {
            $data['progress'] = 'termine';
        }

        TestCase::where('id', $id)->where('template_id', $this->template->id)->update($data);
        unset($this->rows);
    }

    public function clearColumnData(string $field): void
    {
        $this->authorize('update', $this->project);

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
        session()->flash('success', 'Toutes les données de la colonne ont été vidées avec succès.');
    }

    public function updateCell(int $id, string $field, string $value): void
    {
        $this->ensureCanEditData();

        $user = auth()->user();

        if ($user->hasRole('client')) {
            $testCase = TestCase::find($id);
            if (! $testCase) {
                return;
            }

            $isAddedByClient = isset($testCase->data['_added_by_client']) && $testCase->data['_added_by_client'];
            $clientEditableKeywords = ['client', 'uat', 'retour_client', 'commentaires', 'nature'];

            $isEditable = $isAddedByClient;
            if (! $isEditable) {
                foreach ($clientEditableKeywords as $keyword) {
                    if (str_contains(strtolower($field), $keyword)) {
                        $isEditable = true;
                        break;
                    }
                }
            }

            if (! $isEditable) {
                return;
            }
        }

        if ($user->hasRole('tester')) {
            $testerEditableKeywords = ['result', 'nature', 'comment'];
            $isEditable = false;
            foreach ($testerEditableKeywords as $keyword) {
                if (str_contains(strtolower($field), $keyword)) {
                    $isEditable = true;
                    break;
                }
            }
            if (! $isEditable) {
                return;
            }
        }

        if ($user->hasRole('developer')) {
            if (! str_contains(strtolower($field), 'retour_dev')) {
                return;
            }
        }

        $testCase = TestCase::find($id);
        if ($testCase) {
            $data = $testCase->data ?? [];
            $data[$field] = $value;
            $testCase->data = $data;
            $testCase->save();
        }
    }

    public function deleteRow(int $id): void
    {
        $testCase = TestCase::findOrFail($id);
        $this->authorize('delete', $testCase);

        $testCase->delete();
        unset($this->rows);
    }

    public function addColumn(): void
    {
        $this->authorize('update', $this->project);

        $this->validate([
            'newColumnName' => 'required|string|min:2|max:50',
            'newColumnType' => 'required|in:text,textarea,select,url',
        ]);

        $fields = $this->template->fields;
        $machineName = strtolower(str_replace(' ', '_', $this->newColumnName));

        $options = [];
        if ($this->newColumnType === 'select') {
            $options = ['Option 1', 'Option 2'];
        }

        $newField = [
            'name' => $machineName,
            'label' => mb_strtoupper($this->newColumnName),
            'type' => $this->newColumnType,
            'required' => false,
        ];

        if (! empty($options)) {
            $newField['options'] = $options;
        }

        $fields[] = $newField;

        $this->template->update(['fields' => $fields]);
        $this->newColumnName = '';
        $this->newColumnType = 'text';
        $this->showColumnModal = false;

        $this->template->refresh();
    }

    public function removeColumn(string $columnName): void
    {
        $this->authorize('update', $this->project);

        $fields = $this->template->fields;
        $fields = array_filter($fields, fn ($field) => $field['name'] !== $columnName);

        $this->template->update(['fields' => array_values($fields)]);
        $this->template->refresh();
    }

    public function updateColumnType(string $columnName, string $newType): void
    {
        $this->authorize('update', $this->project);

        $fields = $this->template->fields;

        foreach ($fields as &$field) {
            if ($field['name'] === $columnName) {
                $field['type'] = $newType;
                if ($newType === 'select') {
                    $uniqueValues = TestCase::where('template_id', $this->template->id)
                        ->get()
                        ->pluck("data.{$columnName}")
                        ->filter()
                        ->map(fn ($v) => trim((string) $v))
                        ->unique()
                        ->values()
                        ->toArray();

                    $field['options'] = empty($uniqueValues) ? ['Option 1', 'Option 2'] : $uniqueValues;
                }
                break;
            }
        }

        $this->template->update(['fields' => $fields]);
        $this->template->refresh();
    }

    public function openOptionsEditor(string $columnName): void
    {
        $this->authorize('update', $this->project);

        $this->showColumnModal = false;
        $this->editingOptionsColumn = $columnName;
        $this->editingOptions = [];

        $fields = $this->template->fields;
        foreach ($fields as $field) {
            if ($field['name'] === $columnName) {
                $options = $field['options'] ?? [];
                $colors = $field['option_colors'] ?? [];
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
        $this->authorize('update', $this->project);

        $fields = $this->template->fields;

        foreach ($fields as &$field) {
            if ($field['name'] === $this->editingOptionsColumn) {
                $options = [];
                $colors = [];
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
        $this->authorize('update', $this->project);

        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excelFile.required' => 'Veuillez sélectionner un fichier.',
            'excelFile.mimes' => 'Le fichier doit être un fichier Excel (.xlsx, .xls) ou CSV.',
            'excelFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $path = $this->excelFile->getRealPath();
            $importer = new TestCaseExcelImport($this->template, $this->project->id);
            $count = $importer->import($path);

            $this->importResult = "$count ligne(s) importée(s) avec succès !";
            $this->importError = null;
            $this->excelFile = null;
            $this->template->refresh();
            unset($this->rows);
        } catch (\Exception $e) {
            $this->importError = 'Erreur : '.$e->getMessage();
            $this->importResult = null;
        }
    }

    public function render()
    {
        return view('livewire.excel-test-editor');
    }
}
