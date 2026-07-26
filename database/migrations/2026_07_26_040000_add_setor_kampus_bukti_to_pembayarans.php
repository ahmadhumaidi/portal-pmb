<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->date('tanggal_setor_kampus')->nullable()->after('disetor_ke_kampus');
            $table->text('bukti_setor_kampus_path')->nullable()->after('tanggal_setor_kampus');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_setor_kampus', 'bukti_setor_kampus_path']);
        });
    }
};
