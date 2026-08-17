<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            Historique du Projet
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Journal détaillé de toutes les actions effectuées sur ce projet.
        </p>
    </div>
    
    <div class="px-4 py-5 sm:p-6">
        @if ($activities->isEmpty())
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun historique</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aucune activité n'a encore été enregistrée pour ce projet.</p>
            </div>
        @else
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach ($activities as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if (!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        @php
                                            $iconVerb = \App\Support\ActivityLogHelper::verb($activity->description);
                                            $iconBg = match ($iconVerb) {
                                                'créé' => 'bg-green-500',
                                                'mis à jour' => 'bg-blue-500',
                                                'supprimé' => 'bg-red-500',
                                                'restauré' => 'bg-amber-500',
                                                default => 'bg-purple-500',
                                            };
                                        @endphp
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 {{ $iconBg }}">
                                            @if($iconVerb === 'créé')
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                            @elseif($iconVerb === 'mis à jour')
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            @elseif($iconVerb === 'supprimé')
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            @elseif($iconVerb === 'restauré')
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @else
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            @php
                                                $causer = $activity->causer ? $activity->causer->name : 'Système';
                                                $verb = \App\Support\ActivityLogHelper::verb($activity->description);
                                                $changes = [];
                                                $subjectName = "un élément";
                                                
                                                if ($activity->subject_type === \App\Models\TestCase::class) {
                                                    $newData = $activity->attribute_changes['attributes']['data'] ?? [];
                                                    $oldData = $activity->attribute_changes['old']['data'] ?? [];
                                                    
                                                    foreach($newData as $key => $val) {
                                                        $oldVal = $oldData[$key] ?? null;
                                                        if ($val !== $oldVal) {
                                                            $changes[] = [
                                                                'key' => ucwords(str_replace('_', ' ', $key)),
                                                                'old' => $oldVal,
                                                                'new' => $val,
                                                            ];
                                                        }
                                                    }
                                                    $case = \App\Models\TestCase::find($activity->subject_id);
                                                    $identifier = $case?->data['cas_test']
                                                        ?? $case?->data['test_case']
                                                        ?? $newData['cas_test']
                                                        ?? $newData['test_case']
                                                        ?? "#{$activity->subject_id}";
                                                    $subjectName = "le cas de test « {$identifier} »";
                                                } else {
                                                    $newAttrs = $activity->attribute_changes['attributes'] ?? [];
                                                    $oldAttrs = $activity->attribute_changes['old'] ?? [];
                                                    foreach($newAttrs as $key => $val) {
                                                        if (in_array($key, ['updated_at', 'created_at', 'id'])) continue;
                                                        $oldVal = $oldAttrs[$key] ?? null;
                                                        if ($val !== $oldVal) {
                                                            $changes[] = [
                                                                'key' => ucwords(str_replace('_', ' ', $key)),
                                                                'old' => $oldVal,
                                                                'new' => $val,
                                                            ];
                                                        }
                                                    }
                                                    $projectName = \App\Support\ActivityLogHelper::subjectName($activity->subject_type, $activity->subject_id)
                                                        ?? $project->name;
                                                    $subjectName = "le projet « {$projectName} »";
                                                }
                                                if ($verb !== null) {
                                                    $action = "a {$verb} {$subjectName}";
                                                } else {
                                                    // It's a custom message (like our commit message)
                                                    $action = " : " . $activity->description;
                                                }
                                            @endphp
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $causer }}</span>
                                                {{ $action }}
                                            </p>
                                            
                                            @if(count($changes) > 0)
                                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded border border-gray-100 dark:border-gray-700">
                                                    <ul class="space-y-1.5">
                                                        @foreach($changes as $change)
                                                            <li class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                                                <span class="font-semibold text-gray-700 dark:text-gray-200 min-w-[120px]">{{ $change['key'] }} :</span> 
                                                                <div class="flex items-center flex-wrap gap-2">
                                                                    @if($change['old'])
                                                                        <span class="line-through text-red-500/70 bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</span> 
                                                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                                    @else
                                                                        <span class="text-gray-400 italic">Vide</span>
                                                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                                    @endif
                                                                    <span class="text-green-600 dark:text-green-400 font-medium bg-green-50 dark:bg-green-900/20 px-1.5 py-0.5 rounded">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
