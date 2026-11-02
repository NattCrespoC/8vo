<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class CategoryAndSupplierSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categorías Principales
        $categories = [
            ['name' => 'Herramientas Eléctricas y Baterías', 'code' => 'CAT-HERR-ELEC', 'description' => 'Taladros, amoladoras, rotomartillos y accesorios inalámbricos industriales.'],
            ['name' => 'Ferretería Industrial y Fijaciones', 'code' => 'CAT-FERR-IND', 'description' => 'Pernos de alta resistencia, tuercas, anclajes químicos y soldaduras.'],
            ['name' => 'Materiales de Construcción Pesada', 'code' => 'CAT-CONST-PES', 'description' => 'Fierro corrugado, perfiles metálicos y planchas de acero.'],
            ['name' => 'Seguridad Industrial y EPP', 'code' => 'CAT-SEG-IND', 'description' => 'Cascos certificados, botines dieléctricos, arneses y protección auditiva.'],
            ['name' => 'Pinturas, Resinas y Químicos', 'code' => 'CAT-PINT-QUIM', 'description' => 'Esmaltes epóxicos, anticorrosivos industriales, solventes y selladores.'],
            ['name' => 'Repuestos y Bombas Hidráulicas', 'code' => 'CAT-REP-HIDR', 'description' => 'Válvulas, bombas sumergibles, mangueras de alta presión y acoples.'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['code']] = Category::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // 2. Proveedores Bolivianos (NITs representativos y confiables)
        $suppliers = [
            [
                'nit' => '1028450284',
                'business_name' => 'Importadora Ferretera del Valle S.A.',
                'trade_name' => 'FerreValle',
                'contact_name' => 'Ing. Carlos Claros',
                'phone' => '+591 4 4458890',
                'email' => 'ventas@ferrevalle.com.bo',
                'city' => 'Cochabamba',
                'address' => 'Av. Blanco Galindo Km 4.5',
                'category_id' => $catModels['CAT-HERR-ELEC']->id,
                'risk_score' => 5.00,
            ],
            [
                'nit' => '2049182019',
                'business_name' => 'Corporación Industrial Acero Andino S.R.L.',
                'trade_name' => 'AceroAndino',
                'contact_name' => 'Lic. Patricia Camacho',
                'phone' => '+591 3 3552010',
                'email' => 'corporativo@aceroandino.bo',
                'city' => 'Santa Cruz',
                'address' => 'Parque Industrial PI-22',
                'category_id' => $catModels['CAT-CONST-PES']->id,
                'risk_score' => 3.50,
            ],
            [
                'nit' => '3019284055',
                'business_name' => 'Distribuidora Técnica de Seguridad SegurBol S.A.',
                'trade_name' => 'SegurBol',
                'contact_name' => 'Lic. Marcelo Terrazas',
                'phone' => '+591 2 2781100',
                'email' => 'atencion@segurbol.com.bo',
                'city' => 'La Paz',
                'address' => 'Zona Sopocachi, Calle Belisario Salinas',
                'category_id' => $catModels['CAT-SEG-IND']->id,
                'risk_score' => 4.20,
            ],
            [
                'nit' => '4091827361',
                'business_name' => 'Soluciones Químicas Industriales Pintasur Ltda.',
                'trade_name' => 'Pintasur Industrial',
                'contact_name' => 'Ing. Fernando Rios',
                'phone' => '+591 4 4256789',
                'email' => 'pedidos@pintasur.bo',
                'city' => 'Cochabamba',
                'address' => 'Av. Petrolera Km 6',
                'category_id' => $catModels['CAT-PINT-QUIM']->id,
                'risk_score' => 6.10,
            ],
            [
                'nit' => '5018273645',
                'business_name' => 'Suministros Hidráulicos y Mecánicos BolHidro S.R.L.',
                'trade_name' => 'BolHidro',
                'contact_name' => 'Ing. Roberto Villarroel',
                'phone' => '+591 3 3441234',
                'email' => 'ventas@bolhidro.com.bo',
                'city' => 'Santa Cruz',
                'address' => '4to Anillo y Radial 19',
                'category_id' => $catModels['CAT-REP-HIDR']->id,
                'risk_score' => 8.00,
            ],
            [
                'nit' => '9998887771', // Proveedor sospechoso / fantasma para pruebas anómalas
                'business_name' => 'Comercializadora Rápida El Cóndor Express',
                'trade_name' => 'CondorExpress',
                'contact_name' => 'Javier Morales',
                'phone' => '+591 7 7799881',
                'email' => 'condor_express_temporal@gmail.com',
                'city' => 'Quillacollo',
                'address' => 'Calle Sin Nombre Lote 12',
                'category_id' => $catModels['CAT-HERR-ELEC']->id,
                'risk_score' => 88.50, // Proveedor catalogado de ALTO RIESGO
            ]
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['nit' => $sup['nit']], $sup);
        }
    }
}
