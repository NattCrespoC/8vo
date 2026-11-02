<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catElect = Category::where('code', 'CAT-HERR-ELEC')->firstOrFail();
        $catFerr = Category::where('code', 'CAT-FERR-IND')->firstOrFail();
        $catConst = Category::where('code', 'CAT-CONST-PES')->firstOrFail();
        $catSeg = Category::where('code', 'CAT-SEG-IND')->firstOrFail();
        $catPint = Category::where('code', 'CAT-PINT-QUIM')->firstOrFail();
        $catHidr = Category::where('code', 'CAT-REP-HIDR')->firstOrFail();

        $products = [
            // Herramientas Eléctricas
            [
                'category_id' => $catElect->id,
                'sku' => 'TAL-IMP-850W',
                'name' => 'Taladro Percutor Industrial 850W Heavy Duty',
                'unit_of_measure' => 'PZA',
                'current_stock' => 45,
                'min_stock' => 15,
                'weighted_avg_cost' => 420.00,  // Bolivianos (Bs.)
                'std_dev_cost' => 25.50,        // Desviación estándar
                'last_purchase_price' => 425.00,
                'standard_sale_price' => 610.00,
            ],
            [
                'category_id' => $catElect->id,
                'sku' => 'AMO-ANG-9PUL',
                'name' => 'Amoladora Angular Industrial 9 Pulgadas 2400W',
                'unit_of_measure' => 'PZA',
                'current_stock' => 30,
                'min_stock' => 10,
                'weighted_avg_cost' => 880.00,
                'std_dev_cost' => 45.00,
                'last_purchase_price' => 890.00,
                'standard_sale_price' => 1250.00,
            ],
            [
                'category_id' => $catElect->id,
                'sku' => 'ROT-SDS-1500W',
                'name' => 'Rotomartillo Electroneumático SDS-Plus 1500W',
                'unit_of_measure' => 'PZA',
                'current_stock' => 22,
                'min_stock' => 8,
                'weighted_avg_cost' => 1150.00,
                'std_dev_cost' => 60.00,
                'last_purchase_price' => 1140.00,
                'standard_sale_price' => 1690.00,
            ],

            // Ferretería Industrial
            [
                'category_id' => $catFerr->id,
                'sku' => 'PER-G8-12X2',
                'name' => 'Perno Grado 8 Alta Torsión 1/2 x 2 Pulgadas (Caja 100u)',
                'unit_of_measure' => 'CJ',
                'current_stock' => 120,
                'min_stock' => 30,
                'weighted_avg_cost' => 185.00,
                'std_dev_cost' => 12.00,
                'last_purchase_price' => 180.00,
                'standard_sale_price' => 270.00,
            ],
            [
                'category_id' => $catFerr->id,
                'sku' => 'ELE-6013-18',
                'name' => 'Electrodo para Soldadura E6013 1/8 Pulgada (Caja 20 Kg)',
                'unit_of_measure' => 'CJ',
                'current_stock' => 95,
                'min_stock' => 25,
                'weighted_avg_cost' => 320.00,
                'std_dev_cost' => 18.00,
                'last_purchase_price' => 325.00,
                'standard_sale_price' => 460.00,
            ],

            // Materiales de Construcción
            [
                'category_id' => $catConst->id,
                'sku' => 'FIE-COR-12MM',
                'name' => 'Fierro Corrugado Siderúrgico 12 mm x 12 Metros Barra',
                'unit_of_measure' => 'BAR',
                'current_stock' => 450,
                'min_stock' => 100,
                'weighted_avg_cost' => 64.50,
                'std_dev_cost' => 4.20,
                'last_purchase_price' => 65.00,
                'standard_sale_price' => 88.00,
            ],
            [
                'category_id' => $catConst->id,
                'sku' => 'PER-EST-100X50',
                'name' => 'Perfil Estructural Tubo Negro 100x50x2 mm (Barra 6m)',
                'unit_of_measure' => 'BAR',
                'current_stock' => 180,
                'min_stock' => 40,
                'weighted_avg_cost' => 145.00,
                'std_dev_cost' => 8.50,
                'last_purchase_price' => 142.00,
                'standard_sale_price' => 210.00,
            ],

            // Seguridad Industrial
            [
                'category_id' => $catSeg->id,
                'sku' => 'CAS-SEG-DIE',
                'name' => 'Casco de Seguridad Tipo Jockey con Arnés Dieléctrico ANSI',
                'unit_of_measure' => 'PZA',
                'current_stock' => 210,
                'min_stock' => 50,
                'weighted_avg_cost' => 48.00,
                'std_dev_cost' => 3.50,
                'last_purchase_price' => 47.50,
                'standard_sale_price' => 75.00,
            ],
            [
                'category_id' => $catSeg->id,
                'sku' => 'BOT-SEG-PUN',
                'name' => 'Botín Industrial de Seguridad con Puntera de Acero Talla 41',
                'unit_of_measure' => 'PAR',
                'current_stock' => 85,
                'min_stock' => 20,
                'weighted_avg_cost' => 210.00,
                'std_dev_cost' => 14.00,
                'last_purchase_price' => 215.00,
                'standard_sale_price' => 320.00,
            ],

            // Pinturas y Químicos
            [
                'category_id' => $catPint->id,
                'sku' => 'PIN-EPO-GRIS',
                'name' => 'Pintura Epóxica Industrial Alto Tráfico Gris (Galón)',
                'unit_of_measure' => 'GLN',
                'current_stock' => 70,
                'min_stock' => 15,
                'weighted_avg_cost' => 195.00,
                'std_dev_cost' => 11.50,
                'last_purchase_price' => 190.00,
                'standard_sale_price' => 290.00,
            ],

            // Repuestos Hidráulicos
            [
                'category_id' => $catHidr->id,
                'sku' => 'BOM-SUM-1HP',
                'name' => 'Bomba Sumergible de Achique Aguas Sucias 1 HP',
                'unit_of_measure' => 'PZA',
                'current_stock' => 18,
                'min_stock' => 5,
                'weighted_avg_cost' => 1420.00,
                'std_dev_cost' => 75.00,
                'last_purchase_price' => 1410.00,
                'standard_sale_price' => 2050.00,
            ]
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['sku' => $p['sku']], $p);
        }
    }
}
