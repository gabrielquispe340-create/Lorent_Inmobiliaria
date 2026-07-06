<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            try {
                $hasPK = DB::select("
                    SELECT COUNT(*) as cnt
                    FROM information_schema.table_constraints tc
                    JOIN information_schema.constraint_column_usage ccu
                      ON tc.constraint_name = ccu.constraint_name
                    WHERE tc.table_name = 'propiedades'
                      AND tc.constraint_type = 'PRIMARY KEY'
                      AND ccu.column_name = 'id'
                ");
                if (($hasPK[0]->cnt ?? 0) === 0) {
                    $dupCheck = DB::select("
                        SELECT id FROM propiedades
                        GROUP BY id HAVING COUNT(*) > 1
                    ");
                    if (count($dupCheck) > 0) {
                        $dupIds = array_unique(array_column($dupCheck, 'id'));
                        foreach ($dupIds as $dupId) {
                            $rows = DB::table('propiedades')
                                ->where('id', $dupId)
                                ->orderBy('id')
                                ->get();
                            $first = true;
                            foreach ($rows as $row) {
                                if ($first) { $first = false; continue; }
                                $newId = DB::select(
                                    "SELECT nextval('propiedades_id_seq') as next_id"
                                )[0]->next_id;
                                DB::table('propiedades')
                                    ->where('id', $row->id)
                                    ->whereRaw('ctid = ?', [$row->ctid ?? ''])
                                    ->update(['id' => $newId]);
                            }
                        }
                    }
                    DB::statement('ALTER TABLE propiedades ADD PRIMARY KEY (id)');
                    $maxId = DB::table('propiedades')->max('id') ?? 0;
                    DB::statement("SELECT setval('propiedades_id_seq', {$maxId})");
                }
            } catch (\Throwable $e) {
                // Si falla la verificación, continuar con la creación normal
            }
        }

        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['cliente_id', 'propiedad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
