<div>
    {{-- Slide-over panel overlay --}}
    @if($showPanel)
    <div class="fixed inset-0 z-[100] flex" x-data>
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="togglePanel"></div>
        
        {{-- Panel --}}
        <div class="relative ml-auto flex flex-col w-full max-w-md bg-white dark:bg-gray-900 shadow-2xl h-full border-l border-gray-200 dark:border-gray-700">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-[#8b0000] to-[#cc0000]">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-4-4z"/>
                    </svg>
                    <h2 class="text-white font-bold text-lg">Messagerie</h2>
                    @if($this->unreadTotal > 0)
                        <span class="bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $this->unreadTotal }}</span>
                    @endif
                </div>
                <button wire:click="togglePanel" class="text-white/80 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-1 overflow-hidden">
                {{-- Contact List --}}
                <div class="w-24 flex-shrink-0 flex flex-col border-r border-gray-100 dark:border-gray-800 overflow-y-auto bg-gray-50 dark:bg-gray-800/50">
                    @forelse($this->contacts as $contact)
                    <button 
                        wire:click="selectUser({{ $contact->id }})"
                        class="flex flex-col items-center gap-1 py-3 px-1 hover:bg-gray-100 dark:hover:bg-gray-700 transition relative {{ $selectedUserId === $contact->id ? 'bg-red-50 dark:bg-red-900/20 border-r-2 border-[#8b0000]' : '' }}"
                    >
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                style="background: linear-gradient(135deg, {{ $contact->unread_from > 0 ? '#8b0000, #cc0000' : '#374151, #6b7280' }});">
                                {{ strtoupper(substr($contact->name, 0, 2)) }}
                            </div>
                            @if($contact->unread_from > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#8b0000] text-white text-[9px] rounded-full flex items-center justify-center font-bold">
                                    {{ $contact->unread_from > 9 ? '9+' : $contact->unread_from }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[9px] text-gray-500 dark:text-gray-400 font-medium leading-tight text-center line-clamp-2">
                            {{ explode(' ', $contact->name)[0] }}
                        </span>
                        @php
                            $roleLabel = match($contact->getRoleNames()->first()) {
                                'chef_project' => 'Chef',
                                'tester'       => 'Test.',
                                'developer'    => 'Dev',
                                'client'       => 'Client',
                                default        => '',
                            };
                        @endphp
                        @if($roleLabel)
                            <span class="text-[8px] text-gray-400 dark:text-gray-500">{{ $roleLabel }}</span>
                        @endif
                    </button>
                    @empty
                    <p class="text-xs text-gray-400 p-3 text-center">Aucun contact</p>
                    @endforelse
                </div>
                
                {{-- Conversation Area --}}
                <div class="flex-1 flex flex-col overflow-hidden">
                    @if($selectedUserId && $this->selectedContact)
                        {{-- Conversation header --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-600 to-gray-500 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($this->selectedContact->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white leading-none">{{ $this->selectedContact->name }}</p>
                                <p class="text-xs text-gray-400 capitalize mt-0.5">
                                    {{ match($this->selectedContact->getRoleNames()->first()) {
                                        'chef_project' => 'Chef de Projet',
                                        'tester'       => 'Testeur',
                                        'developer'    => 'Développeur',
                                        'client'       => 'Client',
                                        default        => 'Utilisateur',
                                    } }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- Messages --}}
                        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="messages-container">
                            @forelse($this->conversation as $msg)
                                @php $isMe = $msg->sender_id === auth()->id(); @endphp
                                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[80%]">
                                        <div class="px-3 py-2 rounded-2xl text-sm {{ $isMe 
                                            ? 'bg-[#8b0000] text-white rounded-br-sm' 
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-sm' }}">
                                            {{ $msg->content }}
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                                            {{ $msg->created_at->format('H:i') }}
                                            @if($isMe && $msg->read_at)
                                                · <span class="text-blue-400">Lu</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="flex items-center justify-center h-full">
                                    <p class="text-sm text-gray-400">Début de la conversation</p>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Message input --}}
                        <div class="p-3 border-t border-gray-100 dark:border-gray-800">
                            <form wire:submit="sendMessage" class="flex gap-2">
                                <input 
                                    type="text" 
                                    wire:model="newMessage"
                                    placeholder="Votre message..."
                                    class="flex-1 px-4 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b0000] dark:text-white"
                                >
                                <button type="submit" class="px-4 py-2 bg-[#8b0000] hover:bg-red-800 text-white rounded-xl transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-4-4z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sélectionnez un contact</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">pour démarrer une conversation</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Auto-scroll to bottom on new messages --}}
    <script>
        document.addEventListener('livewire:updated', () => {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        });
    </script>
    @endif
</div>
