<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('google_drive_pembayaran_folder_id')->nullable()->after('google_drive_folder_url');
            $table->text('google_drive_pembayaran_folder_url')->nullable()->after('google_drive_pembayaran_folder_id');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn(['google_drive_pembayaran_folder_id', 'google_drive_pembayaran_folder_url']);
        });
    }
};
