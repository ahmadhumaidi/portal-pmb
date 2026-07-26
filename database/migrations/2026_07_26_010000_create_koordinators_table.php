<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koordinators', function (Blueprint $table) {
            $table->id();
            $table->uuid('koordinator_uuid')->unique();
            $table->string('kode_koordinator', 20)->unique();

            $table->string('nama_koordinator');
            $table->boolean('status_aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koordinators');
    }
};
