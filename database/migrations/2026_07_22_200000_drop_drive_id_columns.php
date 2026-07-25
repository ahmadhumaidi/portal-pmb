<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn([
                'ijazah_drive_id',
                'transkrip_awal_drive_id',
                'ktp_drive_id',
                'kk_drive_id',
                'pas_foto_drive_id',
            ]);
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('bukti_bayar_drive_id');
        });

        Schema::table('hasils', function (Blueprint $table) {
            $table->dropColumn([
                'screenshot_pisn_drive_id',
                'screenshot_satudikti_drive_id',
                'scan_ijazah_drive_id',
                'scan_transkrip_drive_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->text('ijazah_drive_id')->nullable()->after('pas_foto_path');
            $table->text('transkrip_awal_drive_id')->nullable()->after('ijazah_drive_id');
            $table->text('ktp_drive_id')->nullable()->after('transkrip_awal_drive_id');
            $table->text('kk_drive_id')->nullable()->after('ktp_drive_id');
            $table->text('pas_foto_drive_id')->nullable()->after('kk_drive_id');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->text('bukti_bayar_drive_id')->nullable()->after('bukti_bayar_path');
        });

        Schema::table('hasils', function (Blueprint $table) {
            $table->text('screenshot_pisn_drive_id')->nullable()->after('scan_transkrip_path');
            $table->text('screenshot_satudikti_drive_id')->nullable()->after('screenshot_pisn_drive_id');
            $table->text('scan_ijazah_drive_id')->nullable()->after('screenshot_satudikti_drive_id');
            $table->text('scan_transkrip_drive_id')->nullable()->after('scan_ijazah_drive_id');
        });
    }
};
