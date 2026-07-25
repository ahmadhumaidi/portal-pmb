<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsByTable = [
            'kampuses' => ['app_sheet_id'],
            'jurusans' => ['app_sheet_id'],
            'mahasiswas' => ['app_sheet_id', 'app_sheet_created_at'],
            'berkas' => ['app_sheet_id', 'app_sheet_updated_at'],
            'pembayarans' => ['app_sheet_id', 'app_sheet_updated_at'],
            'hasils' => ['app_sheet_id', 'app_sheet_updated_at'],
        ];

        foreach ($columnsByTable as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('kampuses', function (Blueprint $table) {
            if (!Schema::hasColumn('kampuses', 'app_sheet_id')) {
                $table->string('app_sheet_id', 30)->nullable()->unique()->after('kode_kampus');
            }
        });

        Schema::table('jurusans', function (Blueprint $table) {
            if (!Schema::hasColumn('jurusans', 'app_sheet_id')) {
                $table->string('app_sheet_id', 30)->nullable()->unique()->after('kode_jurusan');
            }
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'app_sheet_id')) {
                $table->string('app_sheet_id', 30)->nullable()->unique()->after('kode_pmb');
            }
            if (!Schema::hasColumn('mahasiswas', 'app_sheet_created_at')) {
                $table->timestamp('app_sheet_created_at')->nullable()->after('google_drive_folder_url');
            }
        });

        foreach (['berkas', 'pembayarans', 'hasils'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'app_sheet_id')) {
                    $table->string('app_sheet_id', 30)->nullable()->unique();
                }
                if (!Schema::hasColumn($tableName, 'app_sheet_updated_at')) {
                    $table->timestamp('app_sheet_updated_at')->nullable();
                }
            });
        }
    }
};
