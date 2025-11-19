<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'System Management')</title>
    {{-- Load compiled/published assets if available (published to public/vendor/management-panel) --}}
    @if (file_exists(public_path('vendor/management-panel/css/app.css')))
        <link rel="stylesheet" href="{{ asset('vendor/management-panel/css/app.css') }}">
    @else
        {{-- Fallback to Vite during package development --}}
        @vite(['resources/css/app.css'])
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        // Dark Mode Implementation - Run immediately before page loads
        (function() {
            // Get theme from localStorage or default to system
            function getStoredTheme() {
                return localStorage.getItem('theme') || 'system';
            }
            
            // Get system preference
            function getSystemTheme() {
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            
            // Apply theme immediately to prevent flash
            const storedTheme = getStoredTheme();
            const effectiveTheme = storedTheme === 'system' ? getSystemTheme() : storedTheme;
            
            if (effectiveTheme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 h-full m-0 p-0 transition-colors duration-200">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white dark:bg-black text-gray-800 dark:text-white shadow-lg border-b border-gray-200 dark:border-gray-900">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <h1 class="text-xl font-bold">System Management</h1>
                    </div>
                    @if(management_session('authenticated'))
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ management_session('username') }}
                        </span>
                        <!-- Dark Mode Toggle -->
                        <button type="button" id="theme-toggle" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" title="Toggle theme">
                            <svg id="theme-icon-light" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg id="theme-icon-dark" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg id="theme-icon-system" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('management.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 container mx-auto px-4 py-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-black text-gray-600 dark:text-gray-600 py-4 border-t border-gray-200 dark:border-gray-900">
            <div class="container mx-auto px-4 text-center text-sm">
                <p>&copy; {{ date('Y') }} System Management. Use with caution.</p>
            </div>
        </footer>
    </div>

    <script>
        // Dark Mode Toggle Functionality
        (function() {
            console.log('[Theme] Initializing theme toggle...');
            const themes = ['light', 'dark', 'system'];
            
            // Get theme from localStorage or default to system
            function getStoredTheme() {
                const theme = localStorage.getItem('theme') || 'system';
                console.log('[Theme] getStoredTheme() ->', theme);
                return theme;
            }
            
            // Set theme in localStorage
            function setStoredTheme(theme) {
                console.log('[Theme] setStoredTheme() ->', theme);
                localStorage.setItem('theme', theme);
                console.log('[Theme] localStorage.setItem("theme", "' + theme + '")');
            }
            
            // Get system preference
            function getSystemTheme() {
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const systemTheme = isDark ? 'dark' : 'light';
                console.log('[Theme] getSystemTheme() ->', systemTheme, '(prefers-color-scheme: dark =', isDark + ')');
                return systemTheme;
            }
            
            // Apply theme to document
            function applyTheme(theme) {
                console.log('[Theme] applyTheme() called with theme:', theme);
                const effectiveTheme = theme === 'system' ? getSystemTheme() : theme;
                console.log('[Theme] effectiveTheme:', effectiveTheme);
                
                const htmlElement = document.documentElement;
                const hasDarkClass = htmlElement.classList.contains('dark');
                console.log('[Theme] html element classes before:', htmlElement.className);
                console.log('[Theme] hasDarkClass before:', hasDarkClass);
                
                if (effectiveTheme === 'dark') {
                    htmlElement.classList.add('dark');
                    console.log('[Theme] Added "dark" class to html element');
                } else {
                    htmlElement.classList.remove('dark');
                    console.log('[Theme] Removed "dark" class from html element');
                }
                
                const hasDarkClassAfter = htmlElement.classList.contains('dark');
                console.log('[Theme] html element classes after:', htmlElement.className);
                console.log('[Theme] hasDarkClass after:', hasDarkClassAfter);
                
                // Update icon visibility
                updateThemeIcon(theme);
            }
            
            // Update the theme icon based on current theme
            function updateThemeIcon(theme) {
                console.log('[Theme] updateThemeIcon() called with theme:', theme);
                const lightIcon = document.getElementById('theme-icon-light');
                const darkIcon = document.getElementById('theme-icon-dark');
                const systemIcon = document.getElementById('theme-icon-system');
                
                console.log('[Theme] Icons found:', {
                    lightIcon: !!lightIcon,
                    darkIcon: !!darkIcon,
                    systemIcon: !!systemIcon
                });
                
                if (lightIcon && darkIcon && systemIcon) {
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.add('hidden');
                    systemIcon.classList.add('hidden');
                    
                    if (theme === 'light') {
                        lightIcon.classList.remove('hidden');
                        console.log('[Theme] Showing light icon');
                    } else if (theme === 'dark') {
                        darkIcon.classList.remove('hidden');
                        console.log('[Theme] Showing dark icon');
                    } else {
                        systemIcon.classList.remove('hidden');
                        console.log('[Theme] Showing system icon');
                    }
                } else {
                    console.warn('[Theme] Icons not found!', { lightIcon, darkIcon, systemIcon });
                }
            }
            
            // Cycle through themes
            function cycleTheme() {
                console.log('[Theme] ===== cycleTheme() called =====');
                const currentTheme = getStoredTheme();
                console.log('[Theme] Current theme:', currentTheme);
                const currentIndex = themes.indexOf(currentTheme);
                console.log('[Theme] Current index:', currentIndex);
                const nextIndex = (currentIndex + 1) % themes.length;
                console.log('[Theme] Next index:', nextIndex);
                const newTheme = themes[nextIndex];
                console.log('[Theme] New theme:', newTheme);
                
                setStoredTheme(newTheme);
                applyTheme(newTheme);
                console.log('[Theme] ===== cycleTheme() completed =====');
            }
            
            // Initialize theme on page load
            console.log('[Theme] Initializing theme on page load...');
            const initialTheme = getStoredTheme();
            console.log('[Theme] Initial theme:', initialTheme);
            applyTheme(initialTheme);
            
            // Set up theme toggle button - wait for DOM
            function initToggle() {
                console.log('[Theme] initToggle() called');
                const themeToggle = document.getElementById('theme-toggle');
                console.log('[Theme] theme-toggle element found:', !!themeToggle);
                
                if (themeToggle) {
                    console.log('[Theme] Cloning toggle button to remove existing listeners...');
                    // Remove any existing listeners by cloning
                    const newToggle = themeToggle.cloneNode(true);
                    themeToggle.parentNode.replaceChild(newToggle, themeToggle);
                    console.log('[Theme] Toggle button cloned and replaced');
                    
                    // Add click listener
                    newToggle.addEventListener('click', function(e) {
                        console.log('[Theme] ===== TOGGLE BUTTON CLICKED =====');
                        console.log('[Theme] Event:', e);
                        console.log('[Theme] Event target:', e.target);
                        console.log('[Theme] Event currentTarget:', e.currentTarget);
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('[Theme] Calling cycleTheme()...');
                        cycleTheme();
                        console.log('[Theme] cycleTheme() returned');
                    });
                    console.log('[Theme] Click listener attached to toggle button');
                    
                    // Initial icon update
                    const storedTheme = getStoredTheme();
                    console.log('[Theme] Updating icon for stored theme:', storedTheme);
                    updateThemeIcon(storedTheme);
                } else {
                    console.error('[Theme] theme-toggle element NOT FOUND!');
                }
            }
            
            // Initialize when DOM is ready
            console.log('[Theme] Document readyState:', document.readyState);
            if (document.readyState === 'loading') {
                console.log('[Theme] Waiting for DOMContentLoaded...');
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('[Theme] DOMContentLoaded fired');
                    initToggle();
                });
            } else {
                console.log('[Theme] DOM already ready, calling initToggle() immediately');
                initToggle();
            }
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                console.log('[Theme] System theme changed:', e.matches ? 'dark' : 'light');
                const currentTheme = getStoredTheme();
                console.log('[Theme] Current stored theme:', currentTheme);
                if (currentTheme === 'system') {
                    console.log('[Theme] Applying system theme...');
                    applyTheme('system');
                } else {
                    console.log('[Theme] Ignoring system theme change (not in system mode)');
                }
            });
            
            console.log('[Theme] Theme toggle initialization complete');
        })();
    </script>
    
    {{-- Load compiled/published JS if available, otherwise fallback to Vite dev build --}}
    @if (file_exists(public_path('vendor/management-panel/js/app.js')))
        <script src="{{ asset('vendor/management-panel/js/app.js') }}" defer></script>
    @else
        @vite(['resources/js/app.js'])
    @endif

    @stack('scripts')
</body>
</html>
