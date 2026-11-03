@extends('layouts.app')

@section('title', 'PETIDENTITY - ' . ($report->name ?: 'Ficha de Mascota'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:underline">Inicio</a>
        <span>&rsaquo;</span>
        <a href="{{ route('pet-reports.index', ['type' => $report->type]) }}" class="hover:underline">
            {{ $report->type === 'PERDIDA' ? 'Mascotas Perdidas' : 'Mascotas Encontradas' }}
        </a>
        <span>&rsaquo;</span>
        <span class="text-slate-800 font-semibold">{{ $report->name ?: 'Ficha #' . $report->id }}</span>
    </nav>

    <!-- Ficha Principal en 2 Columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Columna Izquierda: Información de la Mascota (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
                <!-- Encabezado -->
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            @if($report->type === 'PERDIDA')
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-600 text-white shadow-sm flex items-center gap-1.5">
                                    <span>🚨</span> MASCOTA PERDIDA
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-sky-600 text-white shadow-sm flex items-center gap-1.5">
                                    <span>🔍</span> MASCOTA ENCONTRADA / RESCATADA
                                </span>
                            @endif

                            @if($report->status === 'RECUPERADO')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    🎉 RECUPERADA / REUNIDA
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    CASO ACTIVO
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black text-slate-900">
                            {{ $report->name ?: 'Mascota sin nombre registrado' }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Reportado el {{ $report->created_at->format('d/m/Y H:i') }} • Suceso: {{ $report->incident_date->format('d/m/Y') }}
                        </p>
                    </div>

                    @if($report->reward_amount)
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl px-4 py-2.5 text-right shadow-sm">
                            <span class="block text-[11px] font-bold text-amber-800 uppercase">Recompensa</span>
                            <span class="text-xl sm:text-2xl font-black text-amber-900">Bs. {{ number_format($report->reward_amount, 0) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Imagen Principal -->
                <div class="relative w-full h-80 sm:h-96 rounded-2xl overflow-hidden bg-slate-100 shadow-inner">
                    <img 
                        src="{{ $report->effective_photo_url }}" 
                        alt="{{ $report->name }}" 
                        class="w-full h-full object-cover"
                    />
                </div>

                <!-- Tabla de Rasgos Físicos -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider text-xs border-b border-slate-100 pb-2">
                        Taxonomía y Características Físicas
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-[11px] font-medium text-slate-500">Especie</span>
                            <span class="font-bold text-sm text-slate-800">{{ $report->species }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-[11px] font-medium text-slate-500">Raza</span>
                            <span class="font-bold text-sm text-slate-800">{{ $report->breed }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-[11px] font-medium text-slate-500">Tamaño</span>
                            <span class="font-bold text-sm text-slate-800">{{ $report->size }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-[11px] font-medium text-slate-500">Color Primario</span>
                            <span class="font-bold text-sm text-slate-800">{{ $report->primary_color }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-[11px] font-medium text-slate-500">Color Secundario</span>
                            <span class="font-bold text-sm text-slate-800">{{ $report->secondary_color ?: 'No especificado' }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-[11px] font-medium text-slate-500">Fecha del Incidente</span>
                            <span class="font-bold text-sm text-slate-800">{{ $report->incident_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Señas Particulares -->
                @if($report->special_features)
                    <div class="space-y-2 bg-amber-50/60 border border-amber-200/80 p-4 rounded-2xl">
                        <h4 class="text-xs font-bold text-amber-900 uppercase">Señas Particulares</h4>
                        <p class="text-sm text-amber-950 leading-relaxed">{{ $report->special_features }}</p>
                    </div>
                @endif

                <!-- Ubicación Georreferenciada -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                            📍 Lugar del Suceso: {{ $report->address_description }}
                        </h3>
                        <span class="text-xs font-mono text-slate-400">
                            {{ number_format($report->latitude, 4) }}, {{ number_format($report->longitude, 4) }}
                        </span>
                    </div>

                    <div id="single-pet-map" class="h-64 w-full rounded-2xl border border-slate-200 overflow-hidden shadow-inner"></div>
                </div>

                <!-- Información de Contacto -->
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Contacto y Rescate
                    </h3>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="font-bold text-sm text-slate-900">{{ $report->contact_name }}</div>
                            <div class="text-xs text-slate-500">Teléfono: <span class="font-semibold text-slate-800">{{ $report->contact_phone }}</span></div>
                        </div>

                        <div class="flex items-center gap-2">
                            @php
                                $waPhone = preg_replace('/\D/', '', (string)($report->contact_whatsapp ?: $report->contact_phone));
                                if (strlen($waPhone) === 8) {
                                    $waPhone = '591' . $waPhone;
                                }
                                $petDescription = $report->type === 'PERDIDA' ? 'tu mascota perdida (' . ($report->name ?: $report->breed) . ')' : 'la mascota encontrada (' . $report->breed . ')';
                                $waMsg = urlencode("Hola {$report->contact_name}, me contacto mediante PETIDENTITY respecto al reporte de {$petDescription} en {$report->address_description}. ¿Aún está activo?");
                            @endphp

                            <a 
                                href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition"
                            >
                                <span>💬 Contactar por WhatsApp</span>
                            </a>

                            @if($report->status === 'ACTIVO')
                                <form action="{{ route('pet-reports.resolve', $report) }}" method="POST" onsubmit="return confirm('¿Confirmas que la mascota fue recuperada y el caso resuelto?');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-emerald-50 hover:border-emerald-300 text-slate-700 hover:text-emerald-700 rounded-xl text-xs font-semibold transition">
                                        Marcar Resuelta
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Coincidencias Inteligentes (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-wide">
                        <span>🤖</span> Motor IA Multicriterio
                    </span>
                    <span class="text-xs text-slate-400 font-mono">
                        Haversine + Fenotipo
                    </span>
                </div>

                <h2 class="text-xl sm:text-2xl font-black">
                    Posibles Coincidencias Detectadas
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    El sistema cruza reportes opuestos (Perdida &harr; Encontrada) calculando la distancia geodésica y compatibilidad de especie, raza, tamaño y color.
                </p>
            </div>

            <!-- Listado de Coincidencias -->
            <div class="space-y-4">
                @forelse($matches as $match)
                    @php
                        $candidate = $report->isLost() ? $match->foundReport : $match->lostReport;
                        $badge = $match->confidence_badge;
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img 
                                    src="{{ $candidate->effective_photo_url }}" 
                                    alt="{{ $candidate->name }}" 
                                    class="w-16 h-16 rounded-xl object-cover bg-slate-100 flex-shrink-0"
                                />
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900">
                                        {{ $candidate->name ?: ($candidate->type === 'PERDIDA' ? 'Mascota Perdida' : 'Mascota Encontrada') }}
                                    </h4>
                                    <p class="text-xs text-slate-500">
                                        {{ $candidate->breed }} • {{ $candidate->primary_color }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        📍 {{ $candidate->address_description }}
                                    </p>
                                </div>
                            </div>

                            <!-- Puntaje y Badge -->
                            <div class="text-right flex-shrink-0">
                                <span class="text-xl font-black {{ $match->match_score >= 80 ? 'text-emerald-600' : ($match->match_score >= 50 ? 'text-amber-600' : 'text-slate-600') }}">
                                    {{ number_format($match->match_score, 0) }}%
                                </span>
                                <span class="block px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} mt-0.5">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Métricas Desglosadas -->
                        <div class="bg-slate-50 rounded-xl p-3 text-xs space-y-1.5 border border-slate-100">
                            <div class="flex items-center justify-between text-slate-600 font-medium">
                                <span>Distancia Geodésica:</span>
                                <span class="font-bold text-slate-800">{{ number_format($match->geo_distance_km, 2) }} km</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600 font-medium">
                                <span>Afinidad Fenotípica:</span>
                                <span class="font-bold text-slate-800">{{ number_format($match->phenotype_score, 0) }} / 70 pts</span>
                            </div>

                            @if(!empty($match->match_reasons))
                                <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-500 space-y-0.5">
                                    @foreach(array_slice($match->match_reasons, 0, 3) as $reason)
                                        <div>✔ {{ $reason }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <a 
                                href="{{ route('pet-reports.show', $candidate) }}" 
                                class="w-full text-center py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl transition border border-emerald-200"
                            >
                                Comparar y Ver Ficha Candidata &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 text-center border border-slate-200">
                        <div class="text-3xl">🔍</div>
                        <h4 class="text-sm font-bold text-slate-800 mt-2">Aún no hay coincidencias cercanas</h4>
                        <p class="text-xs text-slate-500 mt-1">
                            El sistema evalúa automáticamente cada nuevo reporte registrado en la red.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ (float) $report->latitude }};
        const lng = {{ (float) $report->longitude }};

        const map = L.map('single-pet-map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const iconUrl = '{{ $report->type === "PERDIDA" ? "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" : "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" }}';
        
        const petIcon = L.icon({
            iconUrl: iconUrl,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        L.marker([lat, lng], { icon: petIcon })
            .addTo(map)
            .bindPopup('<b>{{ addslashes($report->name ?: "Mascota") }}</b><br>{{ addslashes($report->address_description) }}')
            .openPopup();
    });
</script>
@endpush
