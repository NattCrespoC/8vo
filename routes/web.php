<?php

declare(strict_types=1);

use App\Http\Controllers\PetReportController;
use App\Models\PetReport;
use Illuminate\Support\Facades\Route;

Route::get('/', [PetReportController::class, 'index'])->name('home');
Route::get('/reportes', [PetReportController::class, 'index'])->name('pet-reports.index');
Route::get('/reportar', [PetReportController::class, 'create'])->name('pet-reports.create');
Route::post('/reportes', [PetReportController::class, 'store'])->name('pet-reports.store');
Route::get('/reportes/{report}', [PetReportController::class, 'show'])->name('pet-reports.show');
Route::post('/reportes/{report}/recuperado', [PetReportController::class, 'markResolved'])->name('pet-reports.resolve');
Route::get('/mis-reportes', [PetReportController::class, 'myReports'])->name('pet-reports.my-reports');

Route::get('/api/pet-reports/geo-pins', function () {
    return response()->json(
        PetReport::query()
            ->active()
            ->select(['id', 'type', 'name', 'species', 'breed', 'latitude', 'longitude', 'address_description', 'primary_photo_url'])
            ->get()
            ->map(fn (PetReport $r) => [
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
            ])
    );
})->name('api.pet-reports.geo-pins');
