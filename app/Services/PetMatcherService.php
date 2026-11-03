<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PetMatch;
use App\Models\PetReport;
use Illuminate\Support\Collection;

class PetMatcherService
{
    private const float EARTH_RADIUS_KM = 6371.0;
    private const float MAX_GEO_RADIUS_KM = 15.0;
    private const float MIN_MATCH_THRESHOLD = 25.0;

    /**
     * Calcula la distancia ortodrómica (Haversine) entre dos coordenadas geográficas en kilómetros.
     */
    public function calculateDistanceKm(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) * (sin($deltaLon / 2) ** 2);

        $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1.0 - $a)));

        return round(self::EARTH_RADIUS_KM * $c, 2);
    }

    /**
     * Calcula el puntaje de proximidad geográfica (0 a 30 puntos).
     */
    public function calculateDistanceScore(float $distanceKm): float
    {
        if ($distanceKm >= self::MAX_GEO_RADIUS_KM) {
            return 0.0;
        }

        $score = 30.0 * (1.0 - ($distanceKm / self::MAX_GEO_RADIUS_KM));

        return round(max(0.0, min(30.0, $score)), 2);
    }

    /**
     * Calcula la compatibilidad fenotípica (0 a 70 puntos) y recopila motivos del cálculo.
     *
     * @return array{score: float, reasons: array<string>}
     */
    public function calculatePhenotypeScore(PetReport $r1, PetReport $r2): array
    {
        // 1. Filtro estricto de especie (Condición excluyente)
        if (mb_strtoupper(trim($r1->species)) !== mb_strtoupper(trim($r2->species))) {
            return [
                'score' => 0.0,
                'reasons' => ['Especies incompatibles (' . $r1->species . ' vs ' . $r2->species . ').'],
            ];
        }

        $score = 25.0; // Puntos base por especie coincidente
        $reasons = ['Misma especie: ' . $r1->species . ' (+25 pts)'];

        // 2. Tamaño (0 a 15 pts)
        $sizeScore = $this->calculateSizeScore($r1->size, $r2->size);
        $score += $sizeScore;
        if ($sizeScore === 15.0) {
            $reasons[] = 'Mismo tamaño (' . $r1->size . ') (+15 pts)';
        } elseif ($sizeScore > 0.0) {
            $reasons[] = 'Tamaño adyacente (' . $r1->size . ' vs ' . $r2->size . ') (+' . $sizeScore . ' pts)';
        }

        // 3. Raza (0 a 15 pts)
        $breedScore = $this->calculateBreedScore($r1->breed, $r2->breed);
        $score += $breedScore;
        if ($breedScore === 15.0) {
            $reasons[] = 'Raza compatible (' . $r1->breed . ') (+15 pts)';
        } elseif ($breedScore > 0.0) {
            $reasons[] = 'Afinidad en raza mestiza / fenotipo (+' . $breedScore . ' pts)';
        }

        // 4. Color Primario (0 a 10 pts)
        $colorScore = $this->calculateColorScore($r1, $r2);
        $score += $colorScore;
        if ($colorScore >= 10.0) {
            $reasons[] = 'Color principal coincidente (' . $r1->primary_color . ') (+10 pts)';
        } elseif ($colorScore > 0.0) {
            $reasons[] = 'Afinidad cromática en tonalidades secundarias (+' . $colorScore . ' pts)';
        }

        // 5. Color Secundario (0 a 5 pts)
        $secColorScore = $this->calculateSecondaryColorScore($r1, $r2);
        $score += $secColorScore;
        if ($secColorScore > 0.0) {
            $reasons[] = 'Color secundario coincidente (+' . $secColorScore . ' pts)';
        }

        return [
            'score' => round(min(70.0, $score), 2),
            'reasons' => $reasons,
        ];
    }

    /**
     * Calcula la compatibilidad integral (0 a 100 puntos) entre dos reportes.
     *
     * @return array{
     *     total_score: float,
     *     phenotype_score: float,
     *     distance_score: float,
     *     distance_km: float,
     *     reasons: array<string>
     * }
     */
    public function evaluateMatch(PetReport $lostReport, PetReport $foundReport): array
    {
        $pheno = $this->calculatePhenotypeScore($lostReport, $foundReport);

        // Si las especies difieren, el score total es 0
        if ($pheno['score'] === 0.0) {
            return [
                'total_score' => 0.0,
                'phenotype_score' => 0.0,
                'distance_score' => 0.0,
                'distance_km' => 0.0,
                'reasons' => $pheno['reasons'],
            ];
        }

        $distanceKm = $this->calculateDistanceKm(
            (float) $lostReport->latitude,
            (float) $lostReport->longitude,
            (float) $foundReport->latitude,
            (float) $foundReport->longitude
        );

        $distanceScore = $this->calculateDistanceScore($distanceKm);

        $reasons = $pheno['reasons'];
        $reasons[] = sprintf(
            'Distancia geográfica: %.2f km (+%.2f pts de proximidad)',
            $distanceKm,
            $distanceScore
        );

        $totalScore = round(min(100.0, $pheno['score'] + $distanceScore), 2);

        return [
            'total_score' => $totalScore,
            'phenotype_score' => $pheno['score'],
            'distance_score' => $distanceScore,
            'distance_km' => $distanceKm,
            'reasons' => $reasons,
        ];
    }

    /**
     * Evalúa un reporte contra todos los candidatos opuestos activos y guarda los emparejamientos.
     *
     * @return Collection<int, PetMatch>
     */
    public function evaluateAndPersistMatches(PetReport $report): Collection
    {
        $oppositeType = $report->isLost() ? 'ENCONTRADA' : 'PERDIDA';

        $candidates = PetReport::query()
            ->where('type', $oppositeType)
            ->where('species', $report->species)
            ->where('status', 'ACTIVO')
            ->where('id', '!=', $report->id)
            ->get();

        $savedMatches = collect();

        foreach ($candidates as $candidate) {
            $lost = $report->isLost() ? $report : $candidate;
            $found = $report->isLost() ? $candidate : $report;

            $evaluation = $this->evaluateMatch($lost, $found);

            if ($evaluation['total_score'] >= self::MIN_MATCH_THRESHOLD) {
                $match = PetMatch::updateOrCreate(
                    [
                        'lost_report_id' => $lost->id,
                        'found_report_id' => $found->id,
                    ],
                    [
                        'match_score' => $evaluation['total_score'],
                        'geo_distance_km' => $evaluation['distance_km'],
                        'phenotype_score' => $evaluation['phenotype_score'],
                        'distance_score' => $evaluation['distance_score'],
                        'match_reasons' => $evaluation['reasons'],
                        'status' => 'SUGERIDO',
                    ]
                );

                $savedMatches->push($match);
            }
        }

        return $savedMatches->sortByDesc('match_score')->values();
    }

    private function calculateSizeScore(string $size1, string $size2): float
    {
        $s1 = mb_strtoupper(trim($size1));
        $s2 = mb_strtoupper(trim($size2));

        if ($s1 === $s2) {
            return 15.0;
        }

        $order = ['PEQUEÑO' => 1, 'MEDIANO' => 2, 'GRANDE' => 3];
        $val1 = $order[$s1] ?? 0;
        $val2 = $order[$s2] ?? 0;

        if ($val1 !== 0 && $val2 !== 0 && abs($val1 - $val2) === 1) {
            return 7.5;
        }

        return 0.0;
    }

    private function calculateBreedScore(string $breed1, string $breed2): float
    {
        $b1 = mb_strtolower(trim($breed1));
        $b2 = mb_strtolower(trim($breed2));

        if ($b1 === $b2) {
            return 15.0;
        }

        if (str_contains($b1, 'mestiz') || str_contains($b2, 'mestiz')) {
            return 8.0;
        }

        if (similar_text($b1, $b2, $percent) && $percent > 75.0) {
            return 12.0;
        }

        return 0.0;
    }

    private function calculateColorScore(PetReport $r1, PetReport $r2): float
    {
        $c1 = mb_strtolower(trim($r1->primary_color));
        $c2 = mb_strtolower(trim($r2->primary_color));

        if ($c1 === $c2) {
            return 10.0;
        }

        $sec1 = mb_strtolower(trim((string) $r1->secondary_color));
        $sec2 = mb_strtolower(trim((string) $r2->secondary_color));

        if ($c1 === $sec2 || $c2 === $sec1) {
            return 6.0;
        }

        return 0.0;
    }

    private function calculateSecondaryColorScore(PetReport $r1, PetReport $r2): float
    {
        if (empty($r1->secondary_color) || empty($r2->secondary_color)) {
            return 0.0;
        }

        $s1 = mb_strtolower(trim($r1->secondary_color));
        $s2 = mb_strtolower(trim($r2->secondary_color));

        if ($s1 === $s2) {
            return 5.0;
        }

        return 0.0;
    }
}
