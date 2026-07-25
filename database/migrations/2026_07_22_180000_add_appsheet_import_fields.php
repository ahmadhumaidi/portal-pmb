<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'agama')) {
                $table->string('agama')->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('mahasiswas', 'kewarganegaraan')) {
                $table->string('kewarganegaraan')->nullable()->after('agama');
            }
            if (!Schema::hasColumn('mahasiswas', 'nama_ibu')) {
                $table->string('nama_ibu')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('mahasiswas', 'asal_sekolah')) {
                $table->string('asal_sekolah')->nullable()->after('nama_ibu');
            }
        });

        Schema::table('hasils', function (Blueprint $table) {
            if (!Schema::hasColumn('hasils', 'status_kelulusan')) {
                $table->string('status_kelulusan')->nullable()->after('link_pddikti');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hasils', function (Blueprint $table) {
            if (Schema::hasColumn('hasils', 'status_kelulusan')) {
                $table->dropColumn('status_kelulusan');
            }
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            foreach (['agama', 'kewarganegaraan', 'nama_ibu', 'asal_sekolah'] as $column) {
                if (Schema::hasColumn('mahasiswas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
