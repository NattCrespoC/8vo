<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('auditor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('risk_level', 20)->default('CRITICO')->index(); // BAJO, MEDIO, CRITICO
            $table->integer('confidence_percentage')->default(90);          // 0 a 100
            $table->text('primary_reason');                                 // Motivo principal detectado por la IA
            $table->decimal('financial_impact_bs', 14, 2)->default(0.00);   // Impacto financiero estimado en Bs.
            $table->text('recommended_action');                             // Acción sugerida por el LLM
            $table->string('status', 35)->default('PENDIENTE_RESOLUCION')->index(); 
            // PENDIENTE_RESOLUCION, APROBADO_CON_JUSTIFICACION, ORDEN_ANULADA
            $table->text('resolution_notes')->nullable();                   // Dictamen escrito por el Gerente/Auditor
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_resolutions');
    }
};
