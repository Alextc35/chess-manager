<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('temporadas', function (Blueprint $table) {
            $table->dropColumn('fecha_fin');
        });

        Schema::table('temporadas', function (Blueprint $table) {
            $table->date('fecha_fin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporadas', function (Blueprint $table) {
            $table->dropColumn('fecha_fin');
        });

        Schema::table('temporadas', function (Blueprint $table) {
            $table->date('fecha_fin'); // NOT nullable, como antes
        });
    }
};
