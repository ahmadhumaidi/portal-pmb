<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->uuid('staff_uuid')->unique();
            $table->string('kode_staff', 20)->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('kampus_id')
                ->nullable()
                ->constrained('kampuses')
                ->nullOnDelete();

            $table->string('nama_staff');
            $table->string('email')->unique();

            $table->enum('role', [
                'admin',
                'operator',
                'keuangan',
                'pimpinan',
            ])->default('operator');

            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};