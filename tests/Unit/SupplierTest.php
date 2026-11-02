<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\QueryException;

it('validates unique supplier nit and calculates default risk', function () {
    $category = Category::first();

    // 1. Crear un proveedor con NIT válido y calcular su riesgo por defecto
    $supplierA = Supplier::create([
        'nit' => '8881234011',
        'business_name' => 'Ferretería Central Cochabamba S.R.L.',
        'contact_name' => 'Lic. Carlos Mendez',
        'email' => 'ventas@ferrecentral.bo',
        'category_id' => $category?->id,
        'risk_score' => 0.00,
    ]);

    $defaultRisk = $supplierA->calculateDefaultRisk();
    expect($defaultRisk)->toBe(5.00);

    // 2. Comprobar que un proveedor con datos incompletos o NIT sospechoso incrementa el riesgo
    $supplierB = new Supplier([
        'nit' => '9990001112',
        'business_name' => 'Proveedor Express Sin Contacto',
        'category_id' => null,
        'contact_name' => null,
        'email' => null,
    ]);
    expect($supplierB->calculateDefaultRisk())->toBe(90.00);

    // 3. Comprobar que no se permite duplicar el NIT (violación de unicidad en base de datos)
    expect(function () use ($category) {
        Supplier::create([
            'nit' => '8881234011', // NIT duplicado de supplierA
            'business_name' => 'Otro Proveedor Duplicado S.R.L.',
            'category_id' => $category?->id,
        ]);
    })->toThrow(QueryException::class);
});
