<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'system' => 'Sistema ERP con Auditoría IA Vectorial en Tiempo Real',
        'company' => 'Importadora y Distribuidora Andina S.R.L.',
        'version' => '1.0.0 (Sprint 1)',
        'authors' => ['Nataly Nicole Crespo', 'Lucio Mercado'],
        'status' => 'OPERATIONAL',
        'pgvector' => '768 dim - HNSW',
        'database' => 'PostgreSQL 16'
    ]);
});
