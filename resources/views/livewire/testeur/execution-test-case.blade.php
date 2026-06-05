<?php

use Livewire\Volt\Component;
use App\Models\TestCase;
use App\Models\Project;
use App\Models\TestExecution;
use App\Services\TestExecutionService;

new class extends Component {
    public TestCase $testCase;
    public Project $project;
    
    public array $data = [];
    public ?string $status = null;
    public ?string $nature = null;
    public ?string $priority = null;
    public ?string $comments = null;

    public function mount(TestCase $testCase, Project $project)
    {
        $this->testCase = $testCase;
        $this->project = $project;
        
        $execution = TestExecution::where('test_case_id', $this->testCase->id)
            ->where('tester_id', auth()->id())
            ->first();
            
        if ($execution) {
            $this->data = $execution->results ?? [];
            $this->status = $execution->status;
            $this->nature = $execution->nature;
            $this->priority = $execution->priority;
            $this->comments = $execution->comments;
        } else {
            // Init with template structure
            $fields = $this->testCase->template->fields ?? [];
            foreach ($fields as $field) {
                if (!isset($this->testCase->data[$field['name']])) continue;
                // Results fields are typically the ones testeur will fill out, 
                // but let's just initialize an empty array for their inputs
            }
        }
    }

    public function updated($property)
    {
        $this->saveExecution();
    }

    public function saveExecution()
    {
        $service = app(TestExecutionService::class);
        $service->upsertExecution($this->testCase, auth()->user(), [
            'results' => $this->data,
            'status' => $this->status,
            'nature' => $this->nature,
            'priority' => $this->priority,
            'comments' => $this->comments,
        ]);
        
        // Flash a quick message or just let it autosave silently
    }
}; ?>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Éditeur de Tests</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Cas de test pour {{ $project->name }}</p>
        </div>
        <div wire:loading class="text-sm text-gray-500 flex items-center gap-2">
            <svg class="animate-spin w-4 h-4 text-[#CC0000]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Enregistrement...
        </div>
        <div wire:loading.remove class="text-sm text-green-600 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            À jour
        </div>
    </div>

    <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Render the fields from the test case --}}
            @php $fields = $testCase->template->fields ?? []; @endphp
            @foreach($fields as $field)
                @if(in_array($field['name'], ['cas_test', 'modules', 'fonctionnalites', 'scenarios_test', 'resultats_attendus']))
                <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ $field['label'] }}</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $testCase->data[$field['name']] ?? '—' }}</p>
                </div>
                @endif
            @endforeach

            {{-- Execution & Observation --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-600">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#CC0000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Exécution & Observation
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Résultat Obtenu</label>
                        <textarea wire:model.live.debounce.1000ms="data.resultats_obtenus" rows="4" class="w-full text-sm px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#CC0000] focus:border-transparent resize-none transition" placeholder="Détails du résultat lors de l'exécution..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Commentaires / NB</label>
                        <textarea wire:model.live.debounce.1000ms="comments" rows="2" class="w-full text-sm px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#CC0000] focus:border-transparent resize-none transition" placeholder="Commentaires additionnels..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Parameters --}}
            <div class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Paramètres du Test</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Statut Actuel</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="relative flex items-center justify-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $status === 'valide' ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}">
                                <input type="radio" wire:model.live="status" value="valide" class="sr-only">
                                <span class="text-sm font-medium">Validé</span>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $status === 'non_valide' ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}">
                                <input type="radio" wire:model.live="status" value="non_valide" class="sr-only">
                                <span class="text-sm font-medium">Échec</span>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $status === 'sous_reserve' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}">
                                <input type="radio" wire:model.live="status" value="sous_reserve" class="sr-only">
                                <span class="text-sm font-medium">Sous réserve</span>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $status === 'optimisation' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}">
                                <input type="radio" wire:model.live="status" value="optimisation" class="sr-only">
                                <span class="text-sm font-medium">Optimisation</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Nature du test</label>
                        <select wire:model.live="nature" class="w-full text-sm px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#CC0000] focus:border-transparent transition">
                            <option value="">Sélectionner une nature...</option>
                            @foreach(\App\Models\TestExecution::$natures as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Priorité</label>
                        <div class="flex gap-2">
                            @foreach(['P1', 'P2', 'P3'] as $p)
                            <label class="flex-1 relative flex items-center justify-center p-2 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $priority === $p ? 'border-[#CC0000] bg-red-50 dark:bg-red-900/20 text-[#CC0000] font-bold' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 font-medium' }}">
                                <input type="radio" wire:model.live="priority" value="{{ $p }}" class="sr-only">
                                <span class="text-sm">{{ $p }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
