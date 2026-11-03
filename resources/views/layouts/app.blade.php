<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PETIDENTITY - Reencuentro de Mascotas con IA y Geolocalización')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Leaflet Maps CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 0.75rem;
            padding: 0.25rem;
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased font-sans">
    <!-- Navbar Superior -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logotipo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-emerald-600 font-extrabold text-xl tracking-tight">
                        <span class="p-2 bg-emerald-100 rounded-xl">🐾</span>
                        <span>PET<span class="text-slate-900">IDENTITY</span></span>
                    </a>
                    <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Propuesta 2 • IA Geodésica
                    </span>
                </div>

                <!-- Enlaces de navegación -->
                <nav class="flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-700 hover:text-emerald-600 px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        Galería y Mapa
                    </a>
                    <a href="{{ route('pet-reports.create', ['type' => 'PERDIDA']) }}" class="text-sm font-medium text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 px-3 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                        <span>🚨</span>
                        <span class="hidden sm:inline">Perdí mi</span> Mascota
                    </a>
                    <a href="{{ route('pet-reports.create', ['type' => 'ENCONTRADA']) }}" class="text-sm font-medium text-sky-700 bg-sky-50 border border-sky-200 hover:bg-sky-100 px-3 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                        <span>🔍</span>
                        <span class="hidden sm:inline">Encontré una</span> Mascota
                    </a>
                    <a href="{{ route('pet-reports.my-reports') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 px-2.5 py-2 rounded-lg hover:bg-slate-100 transition">
                        Mis Reportes
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Notificaciones Flash -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xl">✅</span>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-lg leading-none font-bold">&times;</button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-lg leading-none font-bold">&times;</button>
            </div>
        </div>
    @endif

    <!-- Contenido Principal -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer Académico -->
    <footer class="bg-white border-t border-slate-200 py-8 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-700">PETIDENTITY</span>
                <span>— Sistema Colaborativo de Reencuentro con IA y Geolocalización</span>
            </div>
            <div>
                <span>Ingeniería de Software 2 • Práctica Empresarial • Prep. y Eval. de Proyectos (PEP)</span>
            </div>
            <div>
                <span>Cochabamba, Bolivia • Noviembre 2026</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
