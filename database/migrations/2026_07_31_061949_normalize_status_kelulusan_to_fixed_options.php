<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $mapToLulusPddikti = ['Lulus', 'Ijazah diterima'];

    public function up(): void
    {
        DB::table('hasils')
            ->whereIn('status_kelulusan', $this->mapToLulusPddikti)
            ->update(['status_kelulusan' => 'LULUS Pddikti']);
    }

    public function down(): void
    {
        // Not reversible: original free-text values ("Lulus" vs "Ijazah diterima")
        // are no longer distinguishable once merged into "LULUS Pddikti".
    }
};
