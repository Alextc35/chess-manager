<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pagos MODIFY estado ENUM('pagado', 'pendiente', 'exento', 'ausencia') NOT NULL DEFAULT 'pendiente'");

            return;
        }

        if ($driver === 'sqlite') {
            $this->rebuildSqlitePagosTable(['pagado', 'pendiente', 'exento', 'ausencia']);
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pagos MODIFY estado ENUM('pagado', 'pendiente', 'exento') NOT NULL DEFAULT 'pendiente'");

            return;
        }

        if ($driver === 'sqlite') {
            $this->rebuildSqlitePagosTable(['pagado', 'pendiente', 'exento']);
        }
    }

    private function rebuildSqlitePagosTable(array $estados): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::create('pagos_tmp', function (Blueprint $table) use ($estados) {
            $table->id();
            $table->foreignId('alumno_id')->constrained()->onDelete('cascade');
            $table->date('mes');
            $table->enum('estado', $estados)->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->decimal('importe', 8, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'mes']);
        });

        DB::statement(
            'INSERT INTO pagos_tmp (id, alumno_id, mes, estado, fecha_pago, importe, observaciones, created_at, updated_at)
             SELECT id, alumno_id, mes, estado, fecha_pago, importe, observaciones, created_at, updated_at
             FROM pagos'
        );

        Schema::drop('pagos');
        Schema::rename('pagos_tmp', 'pagos');

        DB::statement('PRAGMA foreign_keys=ON');
    }
};
