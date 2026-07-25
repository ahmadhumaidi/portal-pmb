<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasils', function (Blueprint $table) {
            $table->id();
            $table->uuid('hasil_uuid')->unique();
            $table->string('kode_hasil', 30)->unique();

            $table->foreignId('mahasiswa_id')
                ->unique()
                ->constrained('mahasiswas')
                ->cascadeOnDelete();

            $table->foreignId('input_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nim')->nullable()->index();
            $table->string('nomor_seri_ijazah')->nullable()->index();
            $table->text('link_pddikti')->nullable();

            $table->text('screenshot_pisn_path')->nullable();
            $table->text('screenshot_satudikti_path')->nullable();
            $table->text('scan_ijazah_path')->nullable();
            $table->text('scan_transkrip_path')->nullable();

            $table->text('screenshot_pisn_drive_id')->nullable();
            $table->text('screenshot_satudikti_drive_id')->nullable();
            $table->text('scan_ijazah_drive_id')->nullable();
            $table->text('scan_transkrip_drive_id')->nullable();

            $table->enum('status_kirim', [
                'belum_siap',
                'siap_dikirim',
                'sudah_dikirim',
                'sudah_diterima',
                'perlu_revisi',
            ])->default('belum_siap');

            $table->dateTime('tanggal_kirim')->nullable();
            $table->string('metode_kirim')->nullable();
            $table->string('nomor_resi')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasils');
    }
};