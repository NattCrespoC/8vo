<?php

declare(strict_types=1);

use App\Models\PetReport;
use App\Services\PetMatcherService;

beforeEach(function () {
    $this->matcher = new PetMatcherService();
});

it('calculates exact Haversine distance between two coordinates in Cochabamba', function () {
    // Plaza 14 de Septiembre (-17.3938, -66.1570) vs Estadio Capriles (-17.3780, -66.1590) -> ~1.77 km
    $distance = $this->matcher->calculateDistanceKm(-17.3938, -66.1570, -17.3780, -66.1590);

    expect($distance)->toBeGreaterThan(1.5)
        ->toBeLessThan(2.0);
});

it('calculates distance score with linear decay up to 15km', function () {
    // 0 km -> 30 pts
    expect($this->matcher->calculateDistanceScore(0.0))->toBe(30.0);

    // 7.5 km -> 15 pts
    expect($this->matcher->calculateDistanceScore(7.5))->toBe(15.0);

    // >= 15 km -> 0 pts
    expect($this->matcher->calculateDistanceScore(15.0))->toBe(0.0);
    expect($this->matcher->calculateDistanceScore(25.0))->toBe(0.0);
});

it('returns 0 phenotype score and 0 total score for different species', function () {
    $dog = new PetReport([
        'species' => 'PERRO',
        'size' => 'MEDIANO',
        'breed' => 'Mestizo',
        'primary_color' => 'Negro',
        'latitude' => -17.3912,
        'longitude' => -66.1558,
    ]);

    $cat = new PetReport([
        'species' => 'GATO',
        'size' => 'MEDIANO',
        'breed' => 'Mestizo',
        'primary_color' => 'Negro',
        'latitude' => -17.3912,
        'longitude' => -66.1558,
    ]);

    $pheno = $this->matcher->calculatePhenotypeScore($dog, $cat);
    expect($pheno['score'])->toBe(0.0);

    $match = $this->matcher->evaluateMatch($dog, $cat);
    expect($match['total_score'])->toBe(0.0);
});

it('calculates max phenotype score for identical characteristics', function () {
    $dog1 = new PetReport([
        'species' => 'PERRO',
        'size' => 'MEDIANO',
        'breed' => 'Cocker Spaniel',
        'primary_color' => 'Dorado',
        'secondary_color' => 'Blanco',
    ]);

    $dog2 = new PetReport([
        'species' => 'PERRO',
        'size' => 'MEDIANO',
        'breed' => 'Cocker Spaniel',
        'primary_color' => 'Dorado',
        'secondary_color' => 'Blanco',
    ]);

    $pheno = $this->matcher->calculatePhenotypeScore($dog1, $dog2);

    // Especie (25) + Tamaño (15) + Raza (15) + Color1 (10) + Color2 (5) = 70 pts
    expect($pheno['score'])->toBe(70.0);
});

it('evaluates match yielding high confidence for identical breed and nearby location', function () {
    $lostDog = new PetReport([
        'type' => 'PERDIDA',
        'species' => 'PERRO',
        'size' => 'MEDIANO',
        'breed' => 'Cocker Spaniel',
        'primary_color' => 'Dorado',
        'secondary_color' => 'Blanco',
        'latitude' => -17.3912000,
        'longitude' => -66.1558000,
    ]);

    $foundDog = new PetReport([
        'type' => 'ENCONTRADA',
        'species' => 'PERRO',
        'size' => 'MEDIANO',
        'breed' => 'Cocker Spaniel',
        'primary_color' => 'Dorado',
        'secondary_color' => 'Blanco',
        'latitude' => -17.3935000,
        'longitude' => -66.1542000, // ~320m de distancia
    ]);

    $result = $this->matcher->evaluateMatch($lostDog, $foundDog);

    expect($result['distance_km'])->toBeLessThan(0.6)
        ->and($result['distance_score'])->toBeGreaterThan(28.0)
        ->and($result['phenotype_score'])->toBe(70.0)
        ->and($result['total_score'])->toBeGreaterThan(95.0);
});
