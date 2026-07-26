<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('berkas')
            ->whereNotNull('transkrip_awal_path')
            ->pluck('transkrip_awal_path')
            ->each(fn (string $path) => Storage::disk('public')->delete($path));

        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn(['transkrip_awal_path', 'transkrip_awal_drive_url']);
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->text('transkrip_awal_path')->nullable()->after('ijazah_drive_url');
            $table->text('transkrip_awal_drive_url')->nullable()->after('transkrip_awal_path');
        });
    }
};
