<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_kampus', function (Blueprint $table) {
            $table->id();
            $table->uuid('setoran_kampus_uuid')->unique();
            $table->string('kode_setoran_kampus', 30)->unique();

            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswas')
                ->cascadeOnDelete();

            $table->foreignId('input_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_setor');
            $table->text('bukti_setor_path')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('mahasiswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_kampus');
    }
};
