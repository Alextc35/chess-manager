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
        Schema::create('temporada_alumno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporada_id')->constrained()->onDelete('cascade');
            $table->foreignId('alumno_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['temporada_id', 'alumno_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporada_alumno');
    }
};
