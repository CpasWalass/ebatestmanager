<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false" class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none" title="Notifications">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-4 h-4 text-white text-xs rounded-full flex items-center justify-center font-bold" style="background:#CC0000; font-size:10px;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak 
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
         
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
            @if($unreadCount > 0)
                <span class="bg-blue-100 text-blue-800 text-[10px] font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $unreadCount }} non lus</span>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($recentMessages as $message)
                <div class="p-4 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->sender->name }}</span>
                        <span class="text-[10px] text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $message->content }}</p>
                    <div class="mt-2 text-right">
                        <button wire:click="markAsRead({{ $message->id }})" class="text-[10px] font-medium text-[#8b0000] hover:text-red-700 dark:text-red-400">Marquer comme lu</button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aucune nouvelle notification
                </div>
            @endforelse
        </div>

        <div class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <button @click="open = false; $wire.openMessagerie()" class="w-full py-2 text-xs font-semibold text-center text-gray-700 dark:text-gray-300 hover:text-[#8b0000] dark:hover:text-red-400 transition">
                Ouvrir la messagerie
            </button>
        </div>
    </div>
</div>
