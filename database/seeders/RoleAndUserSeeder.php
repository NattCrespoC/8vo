<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos Granulares del ERP
        $permissions = [
            // Módulo Compras
            'crear_orden_compra',
            'ver_catalogo_proveedores',
            'editar_proveedores',

            // Módulo Auditoría
            'ver_centro_auditoria',
            'inspeccionar_dictamenes_ia',
            'emitir_acta_auditoria',
            'inspeccionar_kardex',

            // Módulo Almacén
            'registrar_ingreso_almacen',
            'ver_kardex_fisico',
            'registrar_merma',

            // Módulo Gerencia / Resoluciones Críticas
            'aprobar_orden_bloqueada',
            'anular_orden_compra',
            'ver_reportes_financieros',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // 2. Crear Roles y Sincronizar Permisos
        $roleCompras = Role::firstOrCreate(['name' => 'compras', 'guard_name' => 'web']);
        $roleCompras->syncPermissions([
            'crear_orden_compra',
            'ver_catalogo_proveedores',
            'editar_proveedores',
        ]);

        $roleAuditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $roleAuditor->syncPermissions([
            'ver_centro_auditoria',
            'inspeccionar_dictamenes_ia',
            'emitir_acta_auditoria',
            'inspeccionar_kardex',
            'ver_catalogo_proveedores',
        ]);

        $roleAlmacen = Role::firstOrCreate(['name' => 'almacen', 'guard_name' => 'web']);
        $roleAlmacen->syncPermissions([
            'registrar_ingreso_almacen',
            'ver_kardex_fisico',
            'registrar_merma',
        ]);

        $roleGerente = Role::firstOrCreate(['name' => 'gerente', 'guard_name' => 'web']);
        $roleGerente->syncPermissions(Permission::all()); // Gerencia tiene acceso total

        // 3. Usuarios Oficiales de la Investigación y Defensa
        // Gerente General: Nataly Nicole Crespo
        $userNataly = User::updateOrCreate(
            ['email' => 'nataly.crespo@andina.bo'],
            [
                'name' => 'Nataly Nicole Crespo',
                'password' => Hash::make('Andina2026!'),
                'phone' => '+591 70712345',
                'job_title' => 'Gerente General & Lead Architect',
                'is_active' => true,
            ]
        );
        $userNataly->syncRoles([$roleGerente]);

        // Auditor Interno: Lucio Mercado
        $userLucio = User::updateOrCreate(
            ['email' => 'lucio.mercado@andina.bo'],
            [
                'name' => 'Lucio Mercado',
                'password' => Hash::make('Andina2026!'),
                'phone' => '+591 70767890',
                'job_title' => 'Auditor Interno & Lead Researcher',
                'is_active' => true,
            ]
        );
        $userLucio->syncRoles([$roleAuditor]);

        // Operador de Adquisiciones
        $userCompras = User::updateOrCreate(
            ['email' => 'compras@andina.bo'],
            [
                'name' => 'Oficial de Compras Central',
                'password' => Hash::make('Andina2026!'),
                'phone' => '+591 70711223',
                'job_title' => 'Encargado de Adquisiciones',
                'is_active' => true,
            ]
        );
        $userCompras->syncRoles([$roleCompras]);

        // Jefe de Almacén
        $userAlmacen = User::updateOrCreate(
            ['email' => 'almacen@andina.bo'],
            [
                'name' => 'Jefe de Almacén Central',
                'password' => Hash::make('Andina2026!'),
                'phone' => '+591 70733445',
                'job_title' => 'Encargado de Almacenes y Logística',
                'is_active' => true,
            ]
        );
        $userAlmacen->syncRoles([$roleAlmacen]);
    }
}
