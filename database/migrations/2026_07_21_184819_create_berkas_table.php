<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();
            $table->uuid('berkas_uuid')->unique();
            $table->string('kode_berkas', 30)->unique();

            $table->foreignId('mahasiswa_id')
                ->unique()
                ->constrained('mahasiswas')
                ->cascadeOnDelete();

            $table->foreignId('input_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('ijazah_path')->nullable();
            $table->text('transkrip_awal_path')->nullable();
            $table->text('ktp_path')->nullable();
            $table->text('kk_path')->nullable();
            $table->text('pas_foto_path')->nullable();

            $table->text('ijazah_drive_id')->nullable();
            $table->text('transkrip_awal_drive_id')->nullable();
            $table->text('ktp_drive_id')->nullable();
            $table->text('kk_drive_id')->nullable();
            $table->text('pas_foto_drive_id')->nullable();

            $table->enum('status_verifikasi', [
                'belum_upload',
                'belum_lengkap',
                'menunggu_verifikasi',
                'lengkap',
                'perlu_revisi',
            ])->default('belum_upload');

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas');
    }
};