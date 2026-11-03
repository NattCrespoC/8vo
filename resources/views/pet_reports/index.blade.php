@extends('layouts.app')

@section('title', 'PETIDENTITY - Galería y Mapa de Mascotas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Hero Banner & Métricas -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl p-6 sm:p-10 text-white shadow-xl">
        <div class="max-w-3xl space-y-4">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur border border-white/30 tracking-wide uppercase">
                Red Comunitaria de Reencuentro Animal
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Reconectando mascotas perdidas mediante geolocalización e IA.
            </h1>
            <p class="text-emerald-50 text-sm sm:text-base leading-relaxed">
                Publica mascotas extraviadas o encontradas con coordenadas en mapa. Nuestro algoritmo calcula la distancia ortodrómica de Haversine y similitud fenotípica para alertarte de coincidencias inmediatas.
            </p>
        </div>

        <!-- Indicadores Clave -->
        <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-white/20">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-4 text-center">
                <div class="text-2xl sm:text-4xl font-extrabold text-rose-300">{{ $stats['total_perdidas'] }}</div>
                <div class="text-xs sm:text-sm font-medium text-emerald-100 mt-1">🚨 Mascotas Perdidas</div>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-4 text-center">
                <div class="text-2xl sm:text-4xl font-extrabold text-sky-300">{{ $stats['total_encontradas'] }}</div>
                <div class="text-xs sm:text-sm font-medium text-emerald-100 mt-1">🔍 Mascotas Encontradas</div>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-4 text-center">
                <div class="text-2xl sm:text-4xl font-extrabold text-emerald-300">{{ $stats['total_recuperadas'] }}</div>
                <div class="text-xs sm:text-sm font-medium text-emerald-100 mt-1">🎉 Reencuentros Exitosos</div>
            </div>
        </div>
    </div>

    <!-- Mapa Interactivo General -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <span>🗺️</span> Mapa de Reportes Georreferenciados
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Marcadores interactivos en tiempo real con OpenStreetMap (Rojo = Perdida, Azul = Encontrada).
                </p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Perdidas
                </span>
                <span class="flex items-center gap-1.5 text-sky-600 bg-sky-50 px-2.5 py-1 rounded-full border border-sky-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> Encontradas
                </span>
            </div>
        </div>

        <div id="all-pets-map" class="h-80 sm:h-96 w-full rounded-2xl border border-slate-200 overflow-hidden shadow-inner"></div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('home') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="flex-1 w-full relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Buscar por nombre, raza, color o dirección..." 
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                />
                <span class="absolute left-3.5 top-3 text-slate-400">🔍</span>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full md:w-auto">
                <select name="type" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Todos los Estados</option>
                    <option value="PERDIDA" {{ request('type') === 'PERDIDA' ? 'selected' : '' }}>🚨 Perdidas</option>
                    <option value="ENCONTRADA" {{ request('type') === 'ENCONTRADA' ? 'selected' : '' }}>🔍 Encontradas</option>
                </select>

                <select name="species" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Todas las Especies</option>
                    <option value="PERRO" {{ request('species') === 'PERRO' ? 'selected' : '' }}>🐶 Perros</option>
                    <option value="GATO" {{ request('species') === 'GATO' ? 'selected' : '' }}>🐱 Gatos</option>
                    <option value="OTRO" {{ request('species') === 'OTRO' ? 'selected' : '' }}>🐾 Otros</option>
                </select>

                <select name="size" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Cualquier Tamaño</option>
                    <option value="PEQUEÑO" {{ request('size') === 'PEQUEÑO' ? 'selected' : '' }}>Pequeño</option>
                    <option value="MEDIANO" {{ request('size') === 'MEDIANO' ? 'selected' : '' }}>Mediano</option>
                    <option value="GRANDE" {{ request('size') === 'GRANDE' ? 'selected' : '' }}>Grande</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl transition shadow-sm">
                    Filtrar
                </button>

                @if(request()->hasAny(['search', 'type', 'species', 'size']))
                    <a href="{{ route('home') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-800 underline">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Cuadrícula de Reportes de Mascotas -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">
                Reportes Recientes ({{ $reports->total() }})
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('pet-reports.create', ['type' => 'PERDIDA']) }}" class="text-xs font-semibold text-rose-600 hover:underline">
                    + Reportar Pérdida
                </a>
                <span class="text-slate-300">•</span>
                <a href="{{ route('pet-reports.create', ['type' => 'ENCONTRADA']) }}" class="text-xs font-semibold text-sky-600 hover:underline">
                    + Reportar Hallazgo
                </a>
            </div>
        </div>

        @if($reports->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                <div class="text-5xl">🔍</div>
                <h3 class="text-lg font-bold text-slate-800 mt-4">No se encontraron mascotas con esos criterios</h3>
                <p class="text-sm text-slate-500 mt-1">Prueba quitando los filtros o realiza una nueva búsqueda.</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">
                    Ver todos los reportes
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($reports as $report)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col group">
                        <!-- Imagen con Badges -->
                        <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                            <img 
                                src="{{ $report->effective_photo_url }}" 
                                alt="{{ $report->name ?? $report->breed }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                loading="lazy"
                            />
                            
                            <!-- Badge de Tipo -->
                            <div class="absolute top-3 left-3">
                                @if($report->type === 'PERDIDA')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-600 text-white shadow-md flex items-center gap-1">
                                        <span>🚨</span> PERDIDA
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-sky-600 text-white shadow-md flex items-center gap-1">
                                        <span>🔍</span> ENCONTRADA
                                    </span>
                                @endif
                            </div>

                            <!-- Recompensa si existe -->
                            @if($report->reward_amount)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-400 text-amber-950 shadow">
                                        Bs. {{ number_format($report->reward_amount, 0) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Especie / Tamaño pill -->
                            <div class="absolute bottom-3 left-3 flex gap-1">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-black/60 backdrop-blur text-white">
                                    {{ $report->species === 'PERRO' ? '🐶 Perro' : ($report->species === 'GATO' ? '🐱 Gato' : '🐾 ' . $report->species) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-black/60 backdrop-blur text-white">
                                    {{ $report->size }}
                                </span>
                            </div>
                        </div>

                        <!-- Datos del Reporte -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base leading-snug">
                                    {{ $report->name ?: 'Sin nombre asignado' }}
                                </h3>
                                <p class="text-xs text-slate-600 font-medium mt-0.5">
                                    Raza: <span class="text-slate-800">{{ $report->breed }}</span> • Color: <span class="text-slate-800">{{ $report->primary_color }}</span>
                                </p>
                                <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                                    📍 {{ $report->address_description }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                                <span>{{ $report->incident_date->format('d/m/Y') }}</span>
                                <a href="{{ route('pet-reports.show', $report) }}" class="font-semibold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                                    <span>Ver Ficha</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="pt-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pins = @json($mapPins);
        
        // Coordenadas centrales: Cochabamba, Bolivia (-17.3895, -66.1568)
        const defaultCenter = pins.length > 0 ? [pins[0].lat, pins[0].lng] : [-17.3895, -66.1568];
        const map = L.map('all-pets-map').setView(defaultCenter, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Iconos personalizados
        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const blueIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const bounds = [];

        pins.forEach(pin => {
            if (pin.lat && pin.lng) {
                const icon = pin.type === 'PERDIDA' ? redIcon : blueIcon;
                const marker = L.marker([pin.lat, pin.lng], { icon: icon }).addTo(map);
                bounds.push([pin.lat, pin.lng]);

                const popupHtml = `
                    <div style="width: 200px; font-family: sans-serif;">
                        <img src="${pin.photo}" alt="${pin.title}" style="width: 100%; height: 110px; object-fit: cover; border-radius: 8px; margin-bottom: 6px;" />
                        <div style="font-weight: bold; font-size: 13px; color: #1e293b;">${pin.title}</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">${pin.species} • ${pin.breed}</div>
                        <div style="font-size: 11px; color: #475569; margin-top: 4px;">📍 ${pin.address}</div>
                        <div style="margin-top: 8px;">
                            <a href="${pin.url}" style="display: block; text-align: center; background: #10b981; color: white; padding: 4px 8px; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: bold;">
                                Ver Coincidencias &rarr;
                            </a>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupHtml);
            }
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    });
</script>
@endpush

