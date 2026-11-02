<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RoleAndUserSeeder::class);
});

it('assigns correct permissions to auditor and purchases roles', function () {
    $roleCompras = Role::findByName('compras', 'web');
    $roleAuditor = Role::findByName('auditor', 'web');

    // 1. Validar que el rol compras tiene permisos transaccionales
    expect($roleCompras->hasPermissionTo('crear_orden_compra'))->toBeTrue()
        ->and($roleCompras->hasPermissionTo('ver_catalogo_proveedores'))->toBeTrue()
        ->and($roleCompras->hasPermissionTo('editar_proveedores'))->toBeTrue()
        ->and($roleCompras->hasPermissionTo('ver_centro_auditoria'))->toBeFalse(); // Compras no puede auditar

    // 2. Validar que el rol auditor tiene permisos de fiscalización e inspección
    expect($roleAuditor->hasPermissionTo('ver_centro_auditoria'))->toBeTrue()
        ->and($roleAuditor->hasPermissionTo('inspeccionar_dictamenes_ia'))->toBeTrue()
        ->and($roleAuditor->hasPermissionTo('emitir_acta_auditoria'))->toBeTrue()
        ->and($roleAuditor->hasPermissionTo('inspeccionar_kardex'))->toBeTrue()
        ->and($roleAuditor->hasPermissionTo('crear_orden_compra'))->toBeFalse(); // Auditor no emite compras

    // 3. Validar usuarios asignados
    $userCompras = User::where('email', 'compras@andina.bo')->first();
    $userAuditor = User::where('email', 'lucio.mercado@andina.bo')->first();

    expect($userCompras->can('crear_orden_compra'))->toBeTrue()
        ->and($userCompras->can('ver_centro_auditoria'))->toBeFalse();

    expect($userAuditor->can('ver_centro_auditoria'))->toBeTrue()
        ->and($userAuditor->can('crear_orden_compra'))->toBeFalse();
});
