<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MOTOCONS - Sistema de Créditos')</title>

    <!-- JavaFX Default Font (Segoe UI / System Desktop Fonts) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-[#edf0f5] font-sans antialiased text-slate-800 selection:bg-slate-300 selection:text-slate-900">
    <!-- Barra Superior Estilo Desktop / JavaFX Titlebar & Menubar -->
    <header class="bg-gradient-to-b from-slate-50 to-slate-100 border-b border-slate-300 shadow-xs sticky top-0 z-40">
        <!-- Barra de Título de la Aplicación -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center shadow-xs border border-slate-700">
                    <i data-lucide="bike" class="w-4 h-4 text-red-400"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-base font-bold tracking-tight text-slate-900">MOTOCONS</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-sm bg-slate-200 text-slate-700 border border-slate-300 font-semibold uppercase tracking-wider">
                            Módulo Créditos
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 hidden sm:block leading-none">Sistema de Gestión Comercial y Taller Automotriz</p>
                </div>
            </div>

            <!-- Indicadores de Estado del Sistema -->
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-300">
                    <i data-lucide="database" class="w-3.5 h-3.5 text-emerald-600"></i>
                </span>
                <span class="hidden md:inline-flex items-center gap-1 px-2.5 py-1 rounded-sm text-xs font-medium bg-white text-slate-700 border border-slate-300 shadow-2xs">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-500"></i>
                </span>
            </div>
        </div>
    </header>

    <!-- Contenido Principal Estilo Ventana Desktop -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Barra de Estado Inferior Típica de JavaFX (StatusBar) -->
    <footer class="bg-gradient-to-t from-slate-200 to-slate-100 border-t border-slate-300 py-2 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-600">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span>MOTOCONS v1.0.0 &bull; Universidad de Córdoba (Sede Sahagún)</span>
            </div>
            <div class="flex items-center gap-4 text-slate-500 font-mono text-[11px]">
                <span>Entorno: Local / Testing</span>
                <span>&bull;</span>
                <span>PHP 8.5 / Laravel 12</span>
            </div>
        </div>
    </footer>

    <!-- Contenedor para Notificaciones Toast Estilo Desktop Alert -->
    <div id="toast-container" class="fixed bottom-10 right-6 z-50 flex flex-col gap-2 max-w-md w-full pointer-events-none"></div>

    <script>
        // Sistema de notificaciones toast con iconos Lucide
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const isSuccess = type === 'success';
            const isError = type === 'error';

            const bgClass = isSuccess ? 'bg-white border-emerald-400 text-slate-800 shadow-md ring-1 ring-emerald-500/20' :
                           isError ? 'bg-white border-red-400 text-slate-800 shadow-md ring-1 ring-red-500/20' :
                           'bg-white border-sky-400 text-slate-800 shadow-md ring-1 ring-sky-500/20';

            const iconName = isSuccess ? 'check-circle-2' : isError ? 'alert-triangle' : 'info';
            const iconColor = isSuccess ? 'text-emerald-600' : isError ? 'text-red-600' : 'text-sky-600';

            toast.className = `pointer-events-auto p-3.5 rounded-lg border flex items-start gap-3 transform transition-all duration-300 translate-y-2 opacity-0 ${bgClass}`;
            toast.innerHTML = `
                <i data-lucide="${iconName}" class="w-5 h-5 ${iconColor} shrink-0 mt-0.5"></i>
                <div class="flex-1 text-xs font-medium leading-relaxed text-slate-700">${message}</div>
                <button class="text-slate-400 hover:text-slate-600 text-sm ml-2" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;

            container.appendChild(toast);
            if (window.createLucideIcons) window.createLucideIcons();

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
