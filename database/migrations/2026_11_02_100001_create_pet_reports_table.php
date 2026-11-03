<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['PERDIDA', 'ENCONTRADA'])->index();
            $table->string('name')->nullable();
            $table->enum('species', ['PERRO', 'GATO', 'OTRO'])->index();
            $table->string('breed')->default('Mestizo');
            $table->enum('size', ['PEQUEÑO', 'MEDIANO', 'GRANDE'])->index();
            $table->string('primary_color');
            $table->string('secondary_color')->nullable();
            $table->text('special_features')->nullable();
            $table->string('address_description');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->date('incident_date');
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_whatsapp')->nullable();
            $table->decimal('reward_amount', 10, 2)->nullable();
            $table->enum('status', ['ACTIVO', 'RECUPERADO', 'CERRADO'])->default('ACTIVO')->index();
            $table->string('primary_photo_url')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['type', 'species', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_reports');
    }
};
