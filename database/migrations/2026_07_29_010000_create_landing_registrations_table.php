<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp', 30);
            $table->string('email')->nullable();
            $table->string('school')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('kampus_id')->nullable()->constrained('kampuses')->nullOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('education_level', 30)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('baru');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_registrations');
    }
};
