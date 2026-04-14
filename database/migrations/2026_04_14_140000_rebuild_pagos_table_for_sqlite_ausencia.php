<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite' || !Schema::hasTable('pagos')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::create('pagos_tmp_ausencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained()->onDelete('cascade');
            $table->date('mes');
            $table->enum('estado', ['pagado', 'pendiente', 'exento', 'ausencia'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->decimal('importe', 8, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'mes']);
        });

        DB::statement(
            'INSERT INTO pagos_tmp_ausencia (id, alumno_id, mes, estado, fecha_pago, importe, observaciones, created_at, updated_at)
             SELECT id, alumno_id, mes, estado, fecha_pago, importe, observaciones, created_at, updated_at
             FROM pagos'
        );

        Schema::drop('pagos');
        Schema::rename('pagos_tmp_ausencia', 'pagos');

        DB::statement('PRAGMA foreign_keys=ON');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite' || !Schema::hasTable('pagos')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::create('pagos_tmp_original', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained()->onDelete('cascade');
            $table->date('mes');
            $table->enum('estado', ['pagado', 'pendiente', 'exento'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->decimal('importe', 8, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'mes']);
        });

        DB::statement(
            "INSERT INTO pagos_tmp_original (id, alumno_id, mes, estado, fecha_pago, importe, observaciones, created_at, updated_at)
             SELECT id, alumno_id, mes,
                    CASE WHEN estado = 'ausencia' THEN 'exento' ELSE estado END,
                    fecha_pago, importe, observaciones, created_at, updated_at
             FROM pagos"
        );

        Schema::drop('pagos');
        Schema::rename('pagos_tmp_original', 'pagos');

        DB::statement('PRAGMA foreign_keys=ON');
    }
};
