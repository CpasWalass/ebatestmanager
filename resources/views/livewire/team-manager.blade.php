<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold">Gestion de l'Équipe</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Membres, rôles et charge de travail</p>
        </div>
        <button wire:click="$set('showModal', true)" class="px-4 py-2 bg-[#8b0000] hover:bg-red-800 text-white rounded-md font-medium text-sm flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Ajouter un membre
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par nom ou email..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
        </div>
        <select wire:model.live="roleFilter" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
            <option value="">Tous les rôles</option>
            <option value="chef_project">Chef de Projet</option>
            <option value="tester">Testeur</option>
            <option value="developer">Développeur</option>
            <option value="client">Client</option>
        </select>
    </div>

    {{-- Users grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 pb-24">
        @forelse($this->users as $member)
        @php
            $role = $member->getRoleNames()->first();
            $roleData = match($role) {
                'chef_project' => ['label' => 'Chef de Projet', 'color' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
                'tester'       => ['label' => 'Testeur',        'color' => 'bg-blue-100   text-blue-700   dark:bg-blue-900/30   dark:text-blue-300'],
                'developer'    => ['label' => 'Développeur',    'color' => 'bg-green-100  text-green-700  dark:bg-green-900/30  dark:text-green-300'],
                'client'       => ['label' => 'Client',         'color' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
                default        => ['label' => 'Utilisateur',    'color' => 'bg-gray-100   text-gray-700'],
            };
            $avatarColor = match($role) {
                'chef_project' => '#7c3aed',
                'tester'       => '#1d4ed8',
                'developer'    => '#059669',
                'client'       => '#ea580c',
                default        => '#6b7280',
            };
            $workload = $member->active_assignments ?? 0;
            $workloadClass = $workload > 8 ? 'bg-red-500' : ($workload > 3 ? 'bg-yellow-500' : 'bg-green-500');
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition p-5 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                        style="background: linear-gradient(135deg, {{ $avatarColor }}, {{ $avatarColor }}99);">
                        {{ strtoupper(substr($member->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">{{ $member->name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate max-w-[150px]">{{ $member->email }}</p>
                    </div>
                </div>
                @if($member->id !== auth()->id())
                <button wire:click="deleteUser({{ $member->id }})" wire:confirm="Supprimer {{ $member->name }} ?" class="text-gray-300 hover:text-red-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                @endif
            </div>

            <div class="flex items-center justify-between">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleData['color'] }}">{{ $roleData['label'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $workloadClass }}"></span>
                    {{ $workload }} assignation(s)
                </span>
            </div>

            @if($role === 'tester')
            <div class="pt-1 border-t border-gray-100 dark:border-gray-700">
                @php $pct = $workload > 0 ? min(100, $workload * 10) : 0; @endphp
                <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                    <span>Charge de travail</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $workloadClass }} transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="mt-3 text-sm text-gray-400">Aucun membre trouvé</p>
        </div>
        @endforelse
    </div>

    {{-- Add User Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-[#8b0000] to-[#cc0000]">
                    <h3 class="text-lg font-bold text-white">Ajouter un membre à l'équipe</h3>
                    <p class="text-sm text-white/70 mt-0.5">Un mot de passe temporaire sera généré automatiquement.</p>
                </div>
                <form wire:submit="save" class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                        <input type="text" wire:model="name" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse email</label>
                        <input type="email" wire:model="email" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rôle</label>
                        <select wire:model="role" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                            <option value="chef_project">Chef de Projet</option>
                            <option value="tester">Testeur</option>
                            <option value="developer">Développeur</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 py-2 bg-[#8b0000] hover:bg-red-800 text-white rounded-xl font-medium text-sm transition">
                            Créer le membre
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
