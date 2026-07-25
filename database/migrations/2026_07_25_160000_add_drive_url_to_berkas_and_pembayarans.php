<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->text('ijazah_drive_url')->nullable()->after('ijazah_path');
            $table->text('transkrip_awal_drive_url')->nullable()->after('transkrip_awal_path');
            $table->text('ktp_drive_url')->nullable()->after('ktp_path');
            $table->text('kk_drive_url')->nullable()->after('kk_path');
            $table->text('pas_foto_drive_url')->nullable()->after('pas_foto_path');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->text('bukti_bayar_drive_url')->nullable()->after('bukti_bayar_path');
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn([
                'ijazah_drive_url',
                'transkrip_awal_drive_url',
                'ktp_drive_url',
                'kk_drive_url',
                'pas_foto_drive_url',
            ]);
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('bukti_bayar_drive_url');
        });
    }
};
