<?php

namespace App\Livewire;

use App\Jobs\GenerateTestCasesWithAi;
use App\Models\AiGenerationRequest;
use App\Models\Project;
use App\Models\TestCaseTemplate;
use App\Services\GeminiTestCaseGenerator;
use App\Services\TestCaseFieldService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AiTestCaseGenerator extends Component
{
    use WithFileUploads;

    public Project $project;
    public TestCaseTemplate $template;

    public bool $showModal = false;
    public string $inputMode = 'file'; // 'file' | 'text'
    public $sourceFile = null;         // upload temporaire
    public string $workflowText = '';

    public ?int $generationRequestId = null;
    public array $reviewRows = [];      // lignes proposées, éditables
    public string $errorMessage = '';

    public function mount(Project $project, TestCaseTemplate $template): void
    {
        $this->project = $project;
        $this->template = $template;
    }

    #[On('open-ai-generator')]
    public function openModal(): void
    {
        $this->reset(['reviewRows', 'generationRequestId', 'errorMessage', 'workflowText', 'sourceFile']);
        $this->inputMode = 'file';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function setInputMode(string $mode): void
    {
        $this->inputMode = $mode;
    }

    /**
     * Étape 1 : extraction du texte (synchrone, rapide) puis
     * déclenchement du job asynchrone pour l'appel à Gemini.
     */
    public function startGeneration(GeminiTestCaseGenerator $generator): void
    {
        $this->errorMessage = '';

        if (! auth()->user()->hasRole('chef_project')) {
            $this->errorMessage = "Seul le Chef Projet peut générer des cas de test par IA.";
            return;
        }

        try {
            if ($this->inputMode === 'file') {
                $this->validate(['sourceFile' => 'required|file|mimes:pdf,doc,docx,txt|max:10240']);
                $extractedText = $generator->extractTextFromFile($this->sourceFile);
                $inputType = 'file';
                $filename = $this->sourceFile->getClientOriginalName();
            } else {
                $this->validate(['workflowText' => 'required|string|min:3|max:20000']);
                $extractedText = $this->workflowText;
                $inputType = 'text';
                $filename = null;
            }
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            return;
        }

        /*dd([
            'tenant' => tenant(),
            'tenancy' => tenancy()->tenant,
            'user_tenant' => auth()->user()?->tenant_id,
            'project_tenant' => $this->project->tenant_id,
        ]);*/

        $generationRequest = AiGenerationRequest::create([
            'project_id' => $this->project->id,
            'template_id' => $this->template->id,
            'user_id' => auth()->id(),
            'input_type' => $inputType,
            'input_filename' => $filename,
            'extracted_text' => $extractedText,
            'status' => 'pending',
        ]);

        $this->generationRequestId = $generationRequest->id;
        $this->sourceFile = null;

        GenerateTestCasesWithAi::dispatch($generationRequest);
    }

    /**
     * Appelé toutes les 2s par wire:poll tant que la génération est en cours.
     */
    public function refreshStatus(): void
    {
        if (! $this->generationRequestId || $this->reviewRows) {
            return;
        }

        $generationRequest = AiGenerationRequest::find($this->generationRequestId);

        if (! $generationRequest || ! $generationRequest->isFinished()) {
            return;
        }

        if ($generationRequest->status === 'failed') {
            $this->errorMessage = $generationRequest->error_message ?? "La génération a échoué.";
            $this->generationRequestId = null;
            return;
        }

        $this->reviewRows = collect($generationRequest->proposed_cases)
            ->map(fn ($case) => array_merge($case, ['selected' => true]))
            ->all();
    }

    public function toggleSelectAll(bool $value): void
    {
        foreach ($this->reviewRows as $i => $row) {
            $this->reviewRows[$i]['selected'] = $value;
        }
    }

    public function restartGeneration(): void
    {
        $this->reset(['reviewRows', 'generationRequestId', 'errorMessage']);
    }

    /**
     * Persiste les lignes sélectionnées comme de vrais TestCase (source = ia),
     * en réutilisant le même service que la création manuelle et l'import Excel.
     */
  /*  public function confirmSelection(TestCaseFieldService $fieldService): void
    {
        
        $selected = collect($this->reviewRows)->filter(fn ($row) => $row['selected'] ?? false);
        

        foreach ($selected as $row) {
            unset($row['selected']);
            
            $testCase =dd([
    'colonnes_IA' => array_keys($row),
    'colonnes_template' => collect($this->template->fields)
        ->pluck('name')
        ->toArray(),
]); $fieldService->createTestCase($this->template, $row, $this->project->id);
            $testCase->update(['source' => 'ai']);
        }

        $this->showModal = false;
        $this->reset(['reviewRows', 'generationRequestId']);

        $this->dispatch('ai-test-cases-added', count: $selected->count());
    }*/
    public function confirmSelection(TestCaseFieldService $fieldService): void
{
    $selected = collect($this->reviewRows)
        ->filter(fn ($row) => $row['selected'] ?? false);

    foreach ($selected as $row) {

        unset($row['selected']);

        /**
         * Compléter les champs absents générés par l'IA
         * selon le format du template
         */
        $defaultValues = [
            'etat_test' => 'A faire',
            'resultats_obtenus' => '',
            'nature' => 'Concluant',
            'status' => 'Non validé',
            'commentaires' => '',
        ];

        $row = array_merge($defaultValues, $row);


        /**
         * Ajouter automatiquement les champs du template
         * qui seraient encore absents
         */
        $templateFields = collect($this->template->fields)
            ->pluck('name')
            ->toArray();

        $row = array_merge(
            array_fill_keys($templateFields, ''),
            $row
        );


        /**
         * Création du cas de test
         */
        $testCase = $fieldService->createTestCase(
            $this->template,
            $row,
            $this->project->id
        );

        $testCase->update([
            'source' => 'ai'
        ]);
    }


    $this->showModal = false;

    $this->reset([
        'reviewRows',
        'generationRequestId'
    ]);

    $this->dispatch(
        'ai-test-cases-added',
        count: $selected->count()
    );
}

    public function render()
    {
        return view('livewire.ai-test-case-generator');
    }
}
