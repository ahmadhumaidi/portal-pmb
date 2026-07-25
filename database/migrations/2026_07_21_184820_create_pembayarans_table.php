<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->uuid('pembayaran_uuid')->unique();
            $table->string('kode_pembayaran', 30)->unique();

            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswas')
                ->cascadeOnDelete();

            $table->foreignId('input_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('jenis_pembayaran');
            $table->unsignedSmallInteger('angsuran_ke')->nullable();
            $table->date('tanggal_bayar');
            $table->decimal('nominal', 15, 2);

            $table->enum('status_bayar', [
                'menunggu',
                'terverifikasi',
                'ditolak',
                'dibatalkan',
            ])->default('menunggu');

            $table->text('bukti_bayar_path')->nullable();
            $table->text('bukti_bayar_drive_id')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['mahasiswa_id', 'status_bayar']);
            $table->index('tanggal_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};