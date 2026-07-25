<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->uuid('mahasiswa_uuid')->unique();
            $table->string('kode_pmb', 30)->unique();

            $table->foreignId('kampus_id')
                ->constrained('kampuses')
                ->restrictOnDelete();

            $table->foreignId('jurusan_id')
                ->constrained('jurusans')
                ->restrictOnDelete();

            $table->foreignId('pic_staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->foreignId('input_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nama_mahasiswa');
            $table->string('nik', 30)->nullable()->index();
            $table->string('nisn', 20)->nullable()->index();

            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            $table->string('nomor_whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();

            $table->unsignedSmallInteger('tahun_lulus')->nullable();

            $table->decimal('harga_kesepakatan', 15, 2)->default(0);

            $table->enum('status_pendaftaran', [
                'baru',
                'proses',
                'berkas_kurang',
                'siap_registrasi',
                'terdaftar',
                'selesai',
                'dibatalkan',
            ])->default('baru');

            $table->text('keterangan')->nullable();

            $table->string('google_drive_folder_id')->nullable();
            $table->text('google_drive_folder_url')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};