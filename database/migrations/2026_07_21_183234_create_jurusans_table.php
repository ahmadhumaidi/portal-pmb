<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();

            $table->uuid('jurusan_uuid')->unique();
            $table->string('kode_jurusan', 20)->unique();

            $table->foreignId('kampus_id')
                ->constrained('kampuses')
                ->restrictOnDelete();

            $table->string('nama_jurusan');
            $table->string('jenjang', 20)->nullable();
            $table->boolean('status_aktif')->default(true);

            $table->timestamps();

            $table->unique(
                ['kampus_id', 'nama_jurusan'],
                'jurusan_per_kampus_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusans');
    }
};