<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enfrentamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporada_id')->constrained()->onDelete('cascade');
            $table->foreignId('alumno1_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('alumno2_id')->constrained('alumnos')->onDelete('cascade');
            $table->enum('resultado', ['blancas', 'negras', 'tablas'])->nullable();
            $table->date('fecha')->nullable();
            $table->timestamps();

            $table->unique(['temporada_id', 'alumno1_id', 'alumno2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfrentamientos');
    }
};
