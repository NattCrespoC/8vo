<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PetPhoto;
use App\Models\PetReport;
use App\Models\User;
use App\Services\PetMatcherService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuarios Base del Sistema
        $admin = User::firstOrCreate(
            ['email' => 'admin@petidentity.org'],
            [
                'name' => 'Administrador PETIDENTITY',
                'password' => Hash::make('secret'),
                'phone' => '70712345',
                'job_title' => 'Administrador de Plataforma',
                'is_active' => true,
            ]
        );

        $albergue = User::firstOrCreate(
            ['email' => 'rescate@petidentity.org'],
            [
                'name' => 'Albergue Rescate Animal San Roque',
                'password' => Hash::make('secret'),
                'phone' => '71234567',
                'job_title' => 'Coordinador de Refugio',
                'is_active' => true,
            ]
        );

        $ciudadano = User::firstOrCreate(
            ['email' => 'ciudadano@petidentity.org'],
            [
                'name' => 'Carlos Mendoza (Dueño)',
                'password' => Hash::make('secret'),
                'phone' => '79789012',
                'job_title' => 'Tutor Responsable',
                'is_active' => true,
            ]
        );

        // 2. Reportes de Prueba Georreferenciados en Cochabamba
        $seedReports = [
            // PAR 1: Rocky (Perdido en Plaza Colón) vs Encontrado en Av. Heroínas (Distancia: ~320m) -> Match > 88%
            [
                'user_id' => $ciudadano->id,
                'type' => 'PERDIDA',
                'name' => 'Rocky',
                'species' => 'PERRO',
                'breed' => 'Cocker Spaniel',
                'size' => 'MEDIANO',
                'primary_color' => 'Dorado',
                'secondary_color' => 'Blanco',
                'special_features' => 'Orejas largas peludas, collar de cuero rojo con dije de huesito. Muy asustadizo con los autos.',
                'address_description' => 'Plaza Colón, acera este cerca a la fuente, Cochabamba',
                'latitude' => -17.3912000,
                'longitude' => -66.1558000,
                'incident_date' => Carbon::now()->subDays(2),
                'contact_name' => 'Carlos Mendoza',
                'contact_phone' => '79789012',
                'contact_whatsapp' => '59179789012',
                'reward_amount' => 500.00,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=800&auto=format&fit=crop',
            ],
            [
                'user_id' => $albergue->id,
                'type' => 'ENCONTRADA',
                'name' => null,
                'species' => 'PERRO',
                'breed' => 'Cocker Spaniel',
                'size' => 'MEDIANO',
                'primary_color' => 'Dorado',
                'secondary_color' => 'Blanco en pecho',
                'special_features' => 'Tiene orejas largas, collar rojo sin placa, rescatado temblando debajo de un kiosco.',
                'address_description' => 'Av. Heroínas esq. San Martín, frente a la farmacia, Cochabamba',
                'latitude' => -17.3935000,
                'longitude' => -66.1542000,
                'incident_date' => Carbon::now()->subDays(1),
                'contact_name' => 'Albergue San Roque',
                'contact_phone' => '71234567',
                'contact_whatsapp' => '59171234567',
                'reward_amount' => null,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=800&auto=format&fit=crop',
            ],

            // PAR 2: Mishka (Gata perdida en Parque Lincoln) vs Gata Encontrada en Av. América (Distancia: ~600m) -> Match > 85%
            [
                'user_id' => $ciudadano->id,
                'type' => 'PERDIDA',
                'name' => 'Mishka',
                'species' => 'GATO',
                'breed' => 'Siamés',
                'size' => 'PEQUEÑO',
                'primary_color' => 'Crema',
                'secondary_color' => 'Café',
                'special_features' => 'Ojos azules intensos, cola oscura, collar rosado con cascabel. Muy dócil.',
                'address_description' => 'Parque Lincoln, zona norte, calle Tarija, Cochabamba',
                'latitude' => -17.3732000,
                'longitude' => -66.1685000,
                'incident_date' => Carbon::now()->subDays(3),
                'contact_name' => 'Valeria Soliz',
                'contact_phone' => '70745678',
                'contact_whatsapp' => '59170745678',
                'reward_amount' => 200.00,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=800&auto=format&fit=crop',
            ],
            [
                'user_id' => $albergue->id,
                'type' => 'ENCONTRADA',
                'name' => null,
                'species' => 'GATO',
                'breed' => 'Siamés',
                'size' => 'PEQUEÑO',
                'primary_color' => 'Crema',
                'secondary_color' => 'Café oscuro',
                'special_features' => 'Gatita siamesa con ojos celestes, cascabel plateado, resguardada en edificio residencial.',
                'address_description' => 'Av. América y Pando, cerca al supermercado, Cochabamba',
                'latitude' => -17.3755000,
                'longitude' => -66.1640000,
                'incident_date' => Carbon::now()->subDays(2),
                'contact_name' => 'Lucía Morales',
                'contact_phone' => '72233445',
                'contact_whatsapp' => '59172233445',
                'reward_amount' => null,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=800&auto=format&fit=crop',
            ],

            // PAR 3: Thor (Pastor Alemán en La Chimba)
            [
                'user_id' => $ciudadano->id,
                'type' => 'PERDIDA',
                'name' => 'Thor',
                'species' => 'PERRO',
                'breed' => 'Pastor Alemán',
                'size' => 'GRANDE',
                'primary_color' => 'Negro',
                'secondary_color' => 'Café fuego',
                'special_features' => 'Cicatriz leve en lomo derecho, responde enérgicamente a silbidos.',
                'address_description' => 'La Chimba, Av. Beijing casi Tadeo Haenke, Cochabamba',
                'latitude' => -17.4080000,
                'longitude' => -66.1820000,
                'incident_date' => Carbon::now()->subDays(5),
                'contact_name' => 'Roberto Paz',
                'contact_phone' => '76412345',
                'contact_whatsapp' => '59176412345',
                'reward_amount' => 700.00,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?w=800&auto=format&fit=crop',
            ],
            [
                'user_id' => $albergue->id,
                'type' => 'ENCONTRADA',
                'name' => 'Perro Rescatado Sacaba',
                'species' => 'PERRO',
                'breed' => 'Pastor Alemán',
                'size' => 'GRANDE',
                'primary_color' => 'Negro',
                'secondary_color' => 'Café',
                'special_features' => 'Encontrado desorientado en carretera.',
                'address_description' => 'Sacaba Centro, Plaza Principal, Cochabamba',
                'latitude' => -17.4040000,
                'longitude' => -66.0400000, // ~15 km de distancia (Baja Coincidencia Geográfica)
                'incident_date' => Carbon::now()->subDays(4),
                'contact_name' => 'Zoonosis Sacaba',
                'contact_phone' => '71788990',
                'contact_whatsapp' => '59171788990',
                'reward_amount' => null,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?w=800&auto=format&fit=crop',
            ],

            // PAR 4: Luna (Poodle en Cala Cala) vs Encontrada en Estadio (~1.0 km) -> Match > 86%
            [
                'user_id' => $ciudadano->id,
                'type' => 'PERDIDA',
                'name' => 'Luna',
                'species' => 'PERRO',
                'breed' => 'Poodle',
                'size' => 'PEQUEÑO',
                'primary_color' => 'Blanco',
                'secondary_color' => null,
                'special_features' => 'Pelaje rizado recién cortado, moño violeta en la cabeza.',
                'address_description' => 'Cala Cala, Plaza de Cala Cala, Cochabamba',
                'latitude' => -17.3690000,
                'longitude' => -66.1610000,
                'incident_date' => Carbon::now()->subDay(),
                'contact_name' => 'Elena Rojas',
                'contact_phone' => '75988776',
                'contact_whatsapp' => '59175988776',
                'reward_amount' => 300.00,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?w=800&auto=format&fit=crop',
            ],
            [
                'user_id' => $albergue->id,
                'type' => 'ENCONTRADA',
                'name' => null,
                'species' => 'PERRO',
                'breed' => 'Poodle',
                'size' => 'PEQUEÑO',
                'primary_color' => 'Blanco',
                'secondary_color' => null,
                'special_features' => 'Caniche blanca encontrada en graderías del estadio, muy cariñosa.',
                'address_description' => 'Estadio Félix Capriles, puerta norte, Cochabamba',
                'latitude' => -17.3780000,
                'longitude' => -66.1590000,
                'incident_date' => Carbon::now()->subDay(),
                'contact_name' => 'Guardia Estadio',
                'contact_phone' => '72099112',
                'contact_whatsapp' => '59172099112',
                'reward_amount' => null,
                'status' => 'ACTIVO',
                'primary_photo_url' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?w=800&auto=format&fit=crop',
            ],
        ];

        $matcher = app(PetMatcherService::class);
        $createdReports = [];

        foreach ($seedReports as $data) {
            $report = PetReport::create($data);
            $createdReports[] = $report;

            PetPhoto::create([
                'pet_report_id' => $report->id,
                'photo_path' => $data['primary_photo_url'],
                'is_primary' => true,
            ]);
        }

        // 3. Ejecutar Evaluación de Coincidencias Cruzadas
        foreach ($createdReports as $rep) {
            $matcher->evaluateAndPersistMatches($rep);
        }
    }
}
