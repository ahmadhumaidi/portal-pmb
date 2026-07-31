<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('koordinators', function (Blueprint $table) {
            $table->string('password')->nullable()->after('kode_koordinator');
        });

        DB::table('koordinators')->whereNull('password')->update([
            'password' => Hash::make('sukses1'),
        ]);
    }

    public function down(): void
    {
        Schema::table('koordinators', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
