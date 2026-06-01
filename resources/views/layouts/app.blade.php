<!DOCTYPE html>
<html lang="fr" x-data="darkMode()" :class="dark ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EbaTestManager')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-screen flex flex-col">
        <!-- Top Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-red-600 rounded-md flex items-center justify-center">
                            <span class="text-white font-bold text-sm">EB</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">EbaTestManager</span>
                    </div>

                    <!-- Center: Breadcrumb -->
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                        @yield('breadcrumb')
                    </div>

                    <!-- Right: Actions & Dark Mode -->
                    <div class="flex items-center space-x-4">
                        <!-- Search (hidden on mobile) -->
                        <div class="hidden md:flex relative">
                            <input type="text" placeholder="Rechercher un model..." 
                                class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-md pl-3 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>

                        <!-- Multi-Tenant Active Indicator -->
                        <div class="hidden sm:flex items-center space-x-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                            <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Multi-Tenant Active</span>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="toggle()" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg x-show="!dark" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg x-show="dark" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.464 5.464a1 1 0 01.707-.293h.026a1 1 0 010 2 1 1 0 01-.733-1.707zM5 11a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-sm">
                                {{ auth()->user()->name[0] ?? 'U' }}
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Paramètres</a>
                                <hr class="my-1 border-gray-200 dark:border-gray-700">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        <!-- Footer (Optional) -->
        <footer class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-600 dark:text-gray-400">
                &copy; 2026 EbaTestManager. Tous droits réservés.
            </div>
        </footer>
    </div>

    <!-- Dark Mode Script -->
    <script>
        function darkMode() {
            return {
                dark: localStorage.getItem('dark') === 'true',
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('dark', this.dark);
                },
                init() {
                    // Check system preference if no stored preference
                    if (!localStorage.getItem('dark')) {
                        this.dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }
                }
            }
        }
    </script>
</body>
</html>
