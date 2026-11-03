@extends('layouts.app')

@section('title', 'PETIDENTITY - Mis Reportes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                Mis Reportes Publicados
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Administra tus publicaciones de mascotas extraviadas o rescatadas, revisa coincidencias y actualiza el estado.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pet-reports.create', ['type' => 'PERDIDA']) }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                + Reportar Pérdida
            </a>
            <a href="{{ route('pet-reports.create', ['type' => 'ENCONTRADA']) }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                + Reportar Hallazgo
            </a>
        </div>
    </div>

    @if($reports->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
            <div class="text-5xl">📋</div>
            <h3 class="text-base font-bold text-slate-800 mt-3">Aún no has registrado ningún reporte</h3>
            <p class="text-xs text-slate-500 mt-1">Si perdiste o encontraste un animalito, puedes registrarlo ahora mismo.</p>
            <div class="mt-4 flex justify-center gap-3">
                <a href="{{ route('pet-reports.create', ['type' => 'PERDIDA']) }}" class="px-4 py-2 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl hover:bg-rose-100 transition">
                    Publicar Mascota Perdida
                </a>
                <a href="{{ route('pet-reports.create', ['type' => 'ENCONTRADA']) }}" class="px-4 py-2 bg-sky-50 border border-sky-200 text-sky-700 text-xs font-bold rounded-xl hover:bg-sky-100 transition">
                    Publicar Mascota Encontrada
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Mascota</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Taxonomía</th>
                            <th class="py-3 px-4">Ubicación</th>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($reports as $report)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img 
                                            src="{{ $report->effective_photo_url }}" 
                                            alt="{{ $report->name }}" 
                                            class="w-12 h-12 rounded-xl object-cover bg-slate-100 flex-shrink-0"
                                        />
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm">
                                                {{ $report->name ?: 'Sin nombre' }}
                                            </div>
                                            <div class="text-[11px] text-slate-400">
                                                ID #{{ $report->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($report->type === 'PERDIDA')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                            🚨 PERDIDA
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200">
                                            🔍 ENCONTRADA
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div>{{ $report->species }} • {{ $report->breed }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $report->primary_color }} ({{ $report->size }})</div>
                                </td>
                                <td class="py-3 px-4 max-w-xs truncate">
                                    📍 {{ $report->address_description }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    {{ $report->incident_date->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($report->status === 'RECUPERADO')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            RECUPERADO
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            ACTIVO
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('pet-reports.show', $report) }}" class="font-bold text-emerald-600 hover:text-emerald-800 underline">
                                        Ver Ficha / Coincidencias
                                    </a>
                                    @if($report->status === 'ACTIVO')
                                        <form action="{{ route('pet-reports.resolve', $report) }}" method="POST" class="inline" onsubmit="return confirm('¿Confirmas que la mascota fue recuperada?');">
                                            @csrf
                                            <button type="submit" class="text-slate-400 hover:text-emerald-600 font-semibold text-[11px]">
                                                [Marcar Resuelto]
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

