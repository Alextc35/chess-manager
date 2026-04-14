<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->date('fecha_alta')->nullable()->after('fecha_nacimiento');
        });

        DB::table('alumnos')->update([
            'fecha_alta' => DB::raw('COALESCE(DATE(created_at), CURRENT_DATE)'),
        ]);

    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('fecha_alta');
        });
    }
};
