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
        Schema::table('biaya_kampus', function (Blueprint $table) {
            $table->decimal('biaya_wisuda', 15, 2)->default(0)->after('biaya');
            $table->decimal('biaya_almamater', 15, 2)->default(0)->after('biaya_wisuda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kampus', function (Blueprint $table) {
            $table->dropColumn(['biaya_wisuda', 'biaya_almamater']);
        });
    }
};
