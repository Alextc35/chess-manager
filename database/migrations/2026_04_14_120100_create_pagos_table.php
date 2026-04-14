<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
