<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'koordinator_id')) {
                $table->foreignId('koordinator_id')
                    ->nullable()
                    ->after('koordinator_kelas')
                    ->constrained('koordinators')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('mahasiswas', 'koordinator_kelas')) {
                $table->dropColumn('koordinator_kelas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'koordinator_kelas')) {
                $table->string('koordinator_kelas')->nullable()->after('asal_sekolah');
            }

            if (Schema::hasColumn('mahasiswas', 'koordinator_id')) {
                $table->dropConstrainedForeignId('koordinator_id');
            }
        });
    }
};
