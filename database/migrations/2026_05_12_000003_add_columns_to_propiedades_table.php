<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('propiedades', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('propietario_id')->nullable()->constrained('propietarios')->nullOnDelete();
            $table->integer('habitaciones')->default(0);
            $table->integer('banos')->default(0);
            $table->integer('antiguedad')->default(0);
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
        });
    }
    public function down(): void {
        Schema::table('propiedades', function (Blueprint $table) {
            $table->dropColumn(['categoria_id','propietario_id','habitaciones','banos','antiguedad','latitud','longitud']);
        });
    }
};