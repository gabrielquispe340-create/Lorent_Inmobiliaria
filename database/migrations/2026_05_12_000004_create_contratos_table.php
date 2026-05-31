<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('agente_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->enum('tipo_contrato', ['Venta','Alquiler','Anticretico']);
            $table->decimal('monto', 12, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['Activo','Finalizado','Cancelado'])->default('Activo');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('contratos'); }
};