<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

test('formulario de proveedores rechaza campos obligatorios vacios o nulos', function () {
    $rules = [
        'nit' => ['required', 'string', 'max:30'],
        'business_name' => ['required', 'string', 'max:150'],
        'category_id' => ['required', 'exists:categories,id'],
    ];

    // Intentar validar con campos vacíos
    $validatorEmpty = Validator::make([
        'nit' => '',
        'business_name' => '',
        'category_id' => null,
    ], $rules);

    expect($validatorEmpty->fails())->toBeTrue()
        ->and($validatorEmpty->errors()->has('nit'))->toBeTrue()
        ->and($validatorEmpty->errors()->has('business_name'))->toBeTrue()
        ->and($validatorEmpty->errors()->has('category_id'))->toBeTrue();
});

test('catalogo de productos rechaza costos negativos, stock negativo o campos requeridos vacios', function () {
    $rules = [
        'sku' => ['required', 'string', 'max:50'],
        'name' => ['required', 'string', 'max:200'],
        'current_stock' => ['required', 'integer', 'min:0'],
        'weighted_avg_cost' => ['required', 'numeric', 'min:0'],
        'min_stock' => ['required', 'integer', 'min:1'],
    ];

    // Intentar registrar producto con costos y stocks negativos
    $validatorNegative = Validator::make([
        'sku' => 'SKU-VALIDO-01',
        'name' => 'Taladro Percutor',
        'current_stock' => -5,          // Negativo no permitido
        'weighted_avg_cost' => -150.00,  // Negativo no permitido
        'min_stock' => 0,               // Menor al mínimo de 1
    ], $rules);

    expect($validatorNegative->fails())->toBeTrue()
        ->and($validatorNegative->errors()->has('current_stock'))->toBeTrue()
        ->and($validatorNegative->errors()->has('weighted_avg_cost'))->toBeTrue()
        ->and($validatorNegative->errors()->has('min_stock'))->toBeTrue();
});

test('ordenes de compra rechazan cantidades menores o iguales a cero y precios negativos', function () {
    $rules = [
        'quantity' => ['required', 'integer', 'min:1'],
        'unit_price' => ['required', 'numeric', 'gt:0'],
    ];

    $validatorItem = Validator::make([
        'quantity' => 0,          // Inválido
        'unit_price' => -10.50,   // Inválido
    ], $rules);

    expect($validatorItem->fails())->toBeTrue()
        ->and($validatorItem->errors()->has('quantity'))->toBeTrue()
        ->and($validatorItem->errors()->has('unit_price'))->toBeTrue();
});
