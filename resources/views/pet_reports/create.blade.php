@extends('layouts.app')

@section('title', 'PETIDENTITY - Publicar Reporte de Mascota')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
        <!-- Encabezado del Formulario -->
        <div class="border-b border-slate-200 pb-6 mb-6">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
                Paso a Paso
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                Registrar Reporte de Mascota
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Completa las características físicas y haz clic en el mapa para registrar la ubicación exacta. Nuestro algoritmo buscará posibles coincidencias inmediatamente.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-sm">
                <div class="font-bold flex items-center gap-1.5 mb-1">
                    <span>⚠️</span> Por favor corrige los siguientes campos:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pet-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- 1. Tipo de Reporte (Perdida vs Encontrada) -->
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-3">
                    1. ¿Qué deseas reportar? <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="cursor-pointer border-2 rounded-2xl p-4 flex items-center gap-3 transition hover:border-rose-400 has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/50">
                        <input type="radio" name="type" value="PERDIDA" {{ old('type', $defaultType) === 'PERDIDA' ? 'checked' : '' }} class="h-4 w-4 text-rose-600 focus:ring-rose-500" required>
                        <div>
                            <div class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                                <span>🚨</span> Mascota Extraviada / Perdida
                            </div>
                            <div class="text-xs text-slate-500">Se perdió mi mascota y la estoy buscando</div>
                        </div>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-4 flex items-center gap-3 transition hover:border-sky-400 has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/50">
                        <input type="radio" name="type" value="ENCONTRADA" {{ old('type', $defaultType) === 'ENCONTRADA' ? 'checked' : '' }} class="h-4 w-4 text-sky-600 focus:ring-sky-500" required>
                        <div>
                            <div class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                                <span>🔍</span> Mascota Encontrada / Rescatada
                            </div>
                            <div class="text-xs text-slate-500">Encontré un animalito en la calle y busco a su dueño</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Fotografía Principal -->
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">
                    2. Fotografía de la Mascota
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-emerald-500 transition bg-slate-50">
                    <div class="text-4xl mb-2">📸</div>
                    <p class="text-sm text-slate-600 font-medium">Arrastra una imagen o selecciónala desde tu dispositivo</p>
                    <p class="text-xs text-slate-400 mt-1">Formatos permitidos: JPG, PNG, WEBP (Hasta 5MB)</p>
                    <input type="file" name="photo" accept="image/*" class="mt-4 text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>

            <!-- 3. Rasgos Físicos y Taxonomía -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">
                    3. Características Físicas y Taxonomía
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre (Si lo sabes)</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Rocky, Pelusa..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Especie <span class="text-rose-500">*</span></label>
                        <select name="species" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                            <option value="PERRO" {{ old('species') === 'PERRO' ? 'selected' : '' }}>🐶 Perro / Canino</option>
                            <option value="GATO" {{ old('species') === 'GATO' ? 'selected' : '' }}>🐱 Gato / Felino</option>
                            <option value="OTRO" {{ old('species') === 'OTRO' ? 'selected' : '' }}>🐾 Otro animal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Raza</label>
                        <input type="text" name="breed" value="{{ old('breed', 'Mestizo') }}" placeholder="Ej: Mestizo, Labrador..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tamaño Estimado <span class="text-rose-500">*</span></label>
                        <select name="size" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                            <option value="PEQUEÑO" {{ old('size') === 'PEQUEÑO' ? 'selected' : '' }}>Pequeño (menos de 10 kg)</option>
                            <option value="MEDIANO" {{ old('size', 'MEDIANO') === 'MEDIANO' ? 'selected' : '' }}>Mediano (10 a 25 kg)</option>
                            <option value="GRANDE" {{ old('size') === 'GRANDE' ? 'selected' : '' }}>Grande (más de 25 kg)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Color Principal <span class="text-rose-500">*</span></label>
                        <input type="text" name="primary_color" value="{{ old('primary_color') }}" placeholder="Ej: Negro, Café, Blanco, Dorado..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Color Secundario (Opcional)</label>
                        <input type="text" name="secondary_color" value="{{ old('secondary_color') }}" placeholder="Ej: Blanco en pecho, Manchas cafés..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Señas Particulares y Detalles</label>
                    <textarea name="special_features" rows="2" placeholder="Ej: Lleva collar rojo con cascabel, oreja izquierda doblada, cicatriz en lomo, muy amigable..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('special_features') }}</textarea>
                </div>
            </div>

            <!-- 4. Ubicación en Mapa Interactivo -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-sm font-bold text-slate-800">
                        4. Ubicación Georreferenciada (Leaflet + OpenStreetMap) <span class="text-rose-500">*</span>
                    </h3>
                    <span id="coords-badge" class="text-xs font-mono bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200">
                        Coordenadas: -17.3895000, -66.1568000
                    </span>
                </div>

                <p class="text-xs text-slate-500">
                    📍 Haz clic sobre el mapa para colocar el marcador en el lugar donde fue vista o perdida la mascota. Puedes arrastrar el marcador para afinar la posición.
                </p>

                <!-- Contenedor del Mapa Leaflet -->
                <div id="picker-map" class="h-72 w-full rounded-2xl border border-slate-200 overflow-hidden shadow-inner"></div>

                <!-- Inputs Ocultos de Coordenadas -->
                <input type="hidden" name="latitude" id="lat-input" value="{{ old('latitude', -17.3895000) }}">
                <input type="hidden" name="longitude" id="lng-input" value="{{ old('longitude', -66.1568000) }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Referencia o Dirección Urbana <span class="text-rose-500">*</span></label>
                        <input type="text" name="address_description" value="{{ old('address_description') }}" placeholder="Ej: Av. América esq. Pando, cerca a la farmacia..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha del Suceso <span class="text-rose-500">*</span></label>
                        <input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                    </div>
                </div>
            </div>

            <!-- 5. Datos de Contacto y Recompensa -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">
                    5. Datos de Contacto y Recompensa
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre de Contacto <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_name" value="{{ old('contact_name', 'Vecino Protector') }}" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono Móvil <span class="text-rose-500">*</span></label>
                        <input type="tel" name="contact_phone" value="{{ old('contact_phone', '71234567') }}" placeholder="Ej: 71234567" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">WhatsApp (Para botón directo)</label>
                        <input type="tel" name="contact_whatsapp" value="{{ old('contact_whatsapp', '59171234567') }}" placeholder="Ej: 59171234567" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Monto de Recompensa en Bolivianos (Opcional)</label>
                    <div class="relative w-full sm:w-64">
                        <span class="absolute left-3.5 top-2.5 text-sm text-slate-400">Bs.</span>
                        <input type="number" step="10" name="reward_amount" value="{{ old('reward_amount') }}" placeholder="0.00" class="w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <span>🐾 Publicar Reporte y Buscar Coincidencias</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initialLat = parseFloat(document.getElementById('lat-input').value) || -17.3895000;
        const initialLng = parseFloat(document.getElementById('lng-input').value) || -66.1568000;

        const map = L.map('picker-map').setView([initialLat, initialLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('lat-input').value = lat.toFixed(7);
            document.getElementById('lng-input').value = lng.toFixed(7);
            document.getElementById('coords-badge').textContent = `Coordenadas: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        }

        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
@endpush
