<?php

declare(strict_types=1);

use App\Models\PetPhoto;
use App\Models\PetReport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('displays the public gallery with reports and active counters', function () {
    PetReport::create([
        'type' => 'PERDIDA',
        'name' => 'Firulais',
        'species' => 'PERRO',
        'breed' => 'Mestizo',
        'size' => 'MEDIANO',
        'primary_color' => 'Negro',
        'address_description' => 'Plaza Principal',
        'latitude' => -17.3938,
        'longitude' => -66.1570,
        'incident_date' => now()->subDay(),
        'contact_name' => 'Juan Perez',
        'contact_phone' => '70712345',
        'status' => 'ACTIVO',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('PETIDENTITY')
        ->assertSee('Firulais')
        ->assertSee('Mascotas Perdidas');
});

it('filters pet reports by species and status', function () {
    $dog = PetReport::create([
        'type' => 'PERDIDA',
        'name' => 'Perro Perdido',
        'species' => 'PERRO',
        'breed' => 'Labrador',
        'size' => 'GRANDE',
        'primary_color' => 'Dorado',
        'address_description' => 'Zona Norte',
        'latitude' => -17.3800,
        'longitude' => -66.1600,
        'incident_date' => now(),
        'contact_name' => 'Maria',
        'contact_phone' => '71122334',
        'status' => 'ACTIVO',
    ]);

    $cat = PetReport::create([
        'type' => 'ENCONTRADA',
        'name' => 'Gatito Encontrado',
        'species' => 'GATO',
        'breed' => 'Siamés',
        'size' => 'PEQUEÑO',
        'primary_color' => 'Blanco',
        'address_description' => 'Zona Sur',
        'latitude' => -17.4100,
        'longitude' => -66.1700,
        'incident_date' => now(),
        'contact_name' => 'Pedro',
        'contact_phone' => '79988776',
        'status' => 'ACTIVO',
    ]);

    $response = $this->get(route('home', ['species' => 'GATO']));

    $response->assertOk()
        ->assertSee('Gatito Encontrado')
        ->assertDontSee('Perro Perdido');
});

it('validates required fields when publishing a pet report', function () {
    $response = $this->post(route('pet-reports.store'), []);

    $response->assertSessionHasErrors([
        'type',
        'species',
        'size',
        'primary_color',
        'address_description',
        'latitude',
        'longitude',
        'incident_date',
        'contact_name',
        'contact_phone',
    ]);
});

it('creates a pet report with photo and executes automated matching', function () {
    Storage::fake('public');

    // Crear primero un reporte opuesto que coincida
    $existingFound = PetReport::create([
        'type' => 'ENCONTRADA',
        'name' => 'Rescatado',
        'species' => 'PERRO',
        'breed' => 'Poodle',
        'size' => 'PEQUEÑO',
        'primary_color' => 'Blanco',
        'address_description' => 'Av. Heroínas',
        'latitude' => -17.3930,
        'longitude' => -66.1540,
        'incident_date' => now()->subDay(),
        'contact_name' => 'Rescatista',
        'contact_phone' => '71234567',
        'status' => 'ACTIVO',
    ]);

    $file = UploadedFile::fake()->image('pet.jpg', 600, 600);

    $payload = [
        'type' => 'PERDIDA',
        'name' => 'Copito',
        'species' => 'PERRO',
        'breed' => 'Poodle',
        'size' => 'PEQUEÑO',
        'primary_color' => 'Blanco',
        'address_description' => 'Plaza Colón',
        'latitude' => -17.3912,
        'longitude' => -66.1558,
        'incident_date' => now()->format('Y-m-d'),
        'contact_name' => 'Sofia Morales',
        'contact_phone' => '78901234',
        'contact_whatsapp' => '59178901234',
        'reward_amount' => 250,
        'photo' => $file,
    ];

    $response = $this->post(route('pet-reports.store'), $payload);

    $this->assertDatabaseHas('pet_reports', [
        'name' => 'Copito',
        'type' => 'PERDIDA',
        'species' => 'PERRO',
        'reward_amount' => 250.00,
    ]);

    $created = PetReport::where('name', 'Copito')->first();
    expect($created)->not->toBeNull();

    // Debe existir un registro de coincidencia calculado automáticamente
    $this->assertDatabaseHas('pet_matches', [
        'lost_report_id' => $created->id,
        'found_report_id' => $existingFound->id,
    ]);

    $response->assertRedirect(route('pet-reports.show', $created));
});

it('displays pet report detail page with map and matching recommendations', function () {
    $report = PetReport::create([
        'type' => 'PERDIDA',
        'name' => 'Toby',
        'species' => 'PERRO',
        'breed' => 'Golden Retriever',
        'size' => 'GRANDE',
        'primary_color' => 'Dorado',
        'address_description' => 'Parque Fidel Anze',
        'latitude' => -17.3750,
        'longitude' => -66.1620,
        'incident_date' => now()->subDays(2),
        'contact_name' => 'Ana Gomez',
        'contact_phone' => '77889900',
        'contact_whatsapp' => '59177889900',
        'status' => 'ACTIVO',
    ]);

    $response = $this->get(route('pet-reports.show', $report));

    $response->assertOk()
        ->assertSee('Toby')
        ->assertSee('Golden Retriever')
        ->assertSee('Contactar por WhatsApp')
        ->assertSee('Posibles Coincidencias Detectadas');
});

it('allows marking a report as resolved/recuperado', function () {
    $report = PetReport::create([
        'type' => 'PERDIDA',
        'name' => 'Simba',
        'species' => 'GATO',
        'breed' => 'Mestizo',
        'size' => 'PEQUEÑO',
        'primary_color' => 'Naranja',
        'address_description' => 'Av. Blanco Galindo',
        'latitude' => -17.3950,
        'longitude' => -66.1800,
        'incident_date' => now(),
        'contact_name' => 'Luis',
        'contact_phone' => '71112222',
        'status' => 'ACTIVO',
    ]);

    $response = $this->post(route('pet-reports.resolve', $report));

    expect($report->fresh()->status)->toBe('RECUPERADO');
});

it('returns geo-pins json for leaflet map rendering', function () {
    PetReport::create([
        'type' => 'PERDIDA',
        'name' => 'PinTest',
        'species' => 'PERRO',
        'breed' => 'Beagle',
        'size' => 'MEDIANO',
        'primary_color' => 'Tricolor',
        'address_description' => 'Cala Cala',
        'latitude' => -17.3700,
        'longitude' => -66.1600,
        'incident_date' => now(),
        'contact_name' => 'Laura',
        'contact_phone' => '73334444',
        'status' => 'ACTIVO',
    ]);

    $response = $this->get(route('api.pet-reports.geo-pins'));

    $response->assertOk()
        ->assertJsonFragment([
            'title' => 'PinTest',
            'species' => 'PERRO',
            'lat' => -17.37,
            'lng' => -66.16,
        ]);
});
