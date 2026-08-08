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
        Schema::table('setoran_kampus', function (Blueprint $table) {
            $table->string('jenis_setoran')->default('Biaya Pendidikan')->after('mahasiswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setoran_kampus', function (Blueprint $table) {
            $table->dropColumn('jenis_setoran');
        });
    }
};
