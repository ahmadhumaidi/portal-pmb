<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_kampus', function (Blueprint $table) {
            $table->id();
            $table->uuid('biaya_kampus_uuid')->unique();
            $table->string('kode_biaya_kampus', 30)->unique();

            $table->foreignId('kampus_id')
                ->constrained('kampuses')
                ->cascadeOnDelete();

            $table->foreignId('jurusan_id')
                ->nullable()
                ->constrained('jurusans')
                ->cascadeOnDelete();

            $table->decimal('biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->boolean('status_aktif')->default(true);

            $table->timestamps();

            $table->unique(['kampus_id', 'jurusan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_kampus');
    }
};
