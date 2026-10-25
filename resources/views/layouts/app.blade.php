<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MOTOCONS | Sistema de Créditos')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-slate-950 font-sans antialiased text-slate-200">
    <!-- Navbar Superior -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-amber-500 flex items-center justify-center shadow-lg shadow-red-900/40 text-xl font-black text-white">
                    🏍️
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
                        MOTOCONS <span class="text-xs px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 border border-red-500/30 font-medium">Créditos</span>
                    </span>
                    <p class="text-xs text-slate-400 hidden sm:block">Almacén, Concesionario y Taller de Motocicletas</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    PostgreSQL Neon Activo
                </span>
                <span class="hidden md:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                    Arquitectura Hexagonal
                </span>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-900/50 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <p>© {{ date('Y') }} MOTOCONS - Ingeniería de Sistemas, Universidad de Córdoba (Sede Sahagún).</p>
            <div class="flex items-center gap-4">
                <span>Laravel 12 + Tailwind CSS v4</span>
                <span>•</span>
                <span>PHP 8.5</span>
            </div>
        </div>
    </footer>

    <!-- Contenedor para Alertas Toast -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 max-w-md w-full pointer-events-none"></div>

    <script>
        // Función global para mostrar Toasts de notificación
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const isSuccess = type === 'success';
            const isError = type === 'error';
            const isInfo = type === 'info';

            const bgClass = isSuccess ? 'bg-emerald-950/90 border-emerald-700/60 text-emerald-200' :
                           isError ? 'bg-red-950/90 border-red-700/60 text-red-200' :
                           'bg-sky-950/90 border-sky-700/60 text-sky-200';
            
            const icon = isSuccess ? '✅' : isError ? '⚠️' : 'ℹ️';

            toast.className = `pointer-events-auto p-4 rounded-xl border shadow-xl flex items-start gap-3 transform transition-all duration-300 translate-y-2 opacity-0 ${bgClass}`;
            toast.innerHTML = `
                <span class="text-xl">${icon}</span>
                <div class="flex-1 text-sm font-medium leading-relaxed">${message}</div>
                <button class="text-slate-400 hover:text-white" onclick="this.parentElement.remove()">✕</button>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        };
    </script>

    @stack('scripts')
</body>
</html>
