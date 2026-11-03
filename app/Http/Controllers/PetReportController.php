<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PetPhoto;
use App\Models\PetReport;
use App\Services\PetMatcherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PetReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = PetReport::query()->with('photos')->active();

        if ($request->filled('type') && in_array($request->type, ['PERDIDA', 'ENCONTRADA'], true)) {
            $query->where('type', $request->type);
        }

        if ($request->filled('species') && in_array($request->species, ['PERRO', 'GATO', 'OTRO'], true)) {
            $query->where('species', $request->species);
        }

        if ($request->filled('size') && in_array($request->size, ['PEQUEÑO', 'MEDIANO', 'GRANDE'], true)) {
            $query->where('size', $request->size);
        }

        if ($request->filled('search')) {
            $search = '%' . trim((string) $request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('breed', 'like', $search)
                    ->orWhere('primary_color', 'like', $search)
                    ->orWhere('address_description', 'like', $search)
                    ->orWhere('special_features', 'like', $search);
            });
        }

        $reports = $query->latest('incident_date')->paginate(12)->withQueryString();

        // Marcadores georreferenciados para el mapa general (sincronizados con los filtros aplicados)
        $pinsQuery = clone $query;
        $mapPins = $pinsQuery
            ->select(['id', 'type', 'name', 'species', 'breed', 'latitude', 'longitude', 'address_description', 'primary_photo_url'])
            ->get()
            ->map(function (PetReport $r) {
                return [
                    'id' => $r->id,
                    'type' => $r->type,
                    'title' => $r->name ?: ($r->type === 'PERDIDA' ? 'Mascota Perdida' : 'Mascota Encontrada'),
                    'species' => $r->species,
                    'breed' => $r->breed,
                    'lat' => (float) $r->latitude,
                    'lng' => (float) $r->longitude,
                    'address' => $r->address_description,
                    'photo' => $r->effective_photo_url,
                    'url' => route('pet-reports.show', $r->id),
                ];
            });

        // Contadores para el banner estadístico
        $stats = [
            'total_perdidas' => PetReport::where('type', 'PERDIDA')->where('status', 'ACTIVO')->count(),
            'total_encontradas' => PetReport::where('type', 'ENCONTRADA')->where('status', 'ACTIVO')->count(),
            'total_recuperadas' => PetReport::where('status', 'RECUPERADO')->count(),
        ];

        return view('pet_reports.index', compact('reports', 'mapPins', 'stats'));
    }

    public function create(Request $request): View
    {
        $defaultType = $request->get('type', 'PERDIDA');
        if (!in_array($defaultType, ['PERDIDA', 'ENCONTRADA'], true)) {
            $defaultType = 'PERDIDA';
        }

        return view('pet_reports.create', compact('defaultType'));
    }

    public function store(Request $request, PetMatcherService $matcher): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:PERDIDA,ENCONTRADA',
            'name' => 'nullable|string|max:100',
            'species' => 'required|in:PERRO,GATO,OTRO',
            'breed' => 'nullable|string|max:100',
            'size' => 'required|in:PEQUEÑO,MEDIANO,GRANDE',
            'primary_color' => 'required|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'special_features' => 'nullable|string|max:1000',
            'address_description' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'incident_date' => 'required|date|before_or_equal:today',
            'contact_name' => 'required|string|max:100',
            'contact_phone' => 'required|string|max:30',
            'contact_whatsapp' => 'nullable|string|max:30',
            'reward_amount' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $validated['breed'] = $validated['breed'] ?: 'Mestizo';
        $validated['user_id'] = auth()->id();

        // Subida de imagen
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $uploaded = $request->file('photo');
            $storedName = time() . '_' . uniqid() . '.' . $uploaded->getClientOriginalExtension();
            $photoPath = $uploaded->storeAs('pets', $storedName, 'public');
            $validated['primary_photo_url'] = asset('storage/' . $photoPath);
        }

        $report = PetReport::create($validated);

        if ($photoPath) {
            PetPhoto::create([
                'pet_report_id' => $report->id,
                'photo_path' => $photoPath,
                'is_primary' => true,
            ]);
        }

        // Ejecutar motor de coincidencia inteligente
        $matches = $matcher->evaluateAndPersistMatches($report);

        $notice = '¡Reporte registrado con éxito!';
        if ($matches->count() > 0) {
            $notice .= ' Se detectaron ' . $matches->count() . ' posible(s) coincidencia(s) cercana(s).';
        }

        return redirect()->route('pet-reports.show', $report)->with('success', $notice);
    }

    public function show(PetReport $report, PetMatcherService $matcher): View
    {
        $report->load('photos', 'user');

        // Obtener posibles coincidencias calculadas por el motor IA
        if ($report->isLost()) {
            $matches = $report->matchesAsLost()
                ->with(['foundReport.photos'])
                ->orderByDesc('match_score')
                ->take(6)
                ->get();
        } else {
            $matches = $report->matchesAsFound()
                ->with(['lostReport.photos'])
                ->orderByDesc('match_score')
                ->take(6)
                ->get();
        }

        // Si no había coincidencias registradas previamente, evaluar en vivo
        if ($matches->isEmpty()) {
            $matcher->evaluateAndPersistMatches($report);
            $report->refresh();
            $matches = $report->isLost()
                ? $report->matchesAsLost()->with(['foundReport.photos'])->orderByDesc('match_score')->take(6)->get()
                : $report->matchesAsFound()->with(['lostReport.photos'])->orderByDesc('match_score')->take(6)->get();
        }

        return view('pet_reports.show', compact('report', 'matches'));
    }

    public function markResolved(PetReport $report): RedirectResponse
    {
        $report->update(['status' => 'RECUPERADO']);

        return back()->with('success', '¡Excelente noticia! El reporte ha sido marcado como RECUPERADO.');
    }

    public function myReports(): View
    {
        $query = PetReport::query()->with('photos')->latest();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        $reports = $query->paginate(10);

        return view('pet_reports.my_reports', compact('reports'));
    }
}
