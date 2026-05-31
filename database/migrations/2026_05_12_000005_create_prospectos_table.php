<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prospectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('propiedad_id')->nullable()->constrained('propiedades')->nullOnDelete();
            $table->enum('etapa', ['Nuevo','Contactado','Interesado','Negociando','Cerrado','Perdido'])->default('Nuevo');
            $table->text('notas')->nullable();
            $table->date('fecha_contacto')->useCurrent();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('prospectos'); }
};