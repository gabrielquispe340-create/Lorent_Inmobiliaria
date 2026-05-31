<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->enum('tipo_contacto', ['Llamada','Correo','Visita','WhatsApp','Otro']);
            $table->text('descripcion');
            $table->date('fecha')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('seguimientos'); }
};