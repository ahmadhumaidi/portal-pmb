<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('pembayarans')
            ->whereNotNull('disetor_ke_kampus')
            ->orWhereNotNull('bukti_setor_kampus_path')
            ->orderBy('id')
            ->get(['mahasiswa_id', 'input_by', 'disetor_ke_kampus', 'tanggal_setor_kampus', 'bukti_setor_kampus_path', 'created_at', 'updated_at']);

        $nextId = ((int) DB::table('setoran_kampus')->max('id')) + 1;

        foreach ($rows as $row) {
            DB::table('setoran_kampus')->insert([
                'setoran_kampus_uuid' => (string) Str::uuid(),
                'kode_setoran_kampus' => 'STK-' . now()->year . '-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT),
                'mahasiswa_id' => $row->mahasiswa_id,
                'input_by' => $row->input_by,
                'nominal' => $row->disetor_ke_kampus ?? 0,
                'tanggal_setor' => $row->tanggal_setor_kampus ?? ($row->created_at ? substr($row->created_at, 0, 10) : now()->toDateString()),
                'bukti_setor_path' => $row->bukti_setor_kampus_path,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $nextId++;
        }

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['disetor_ke_kampus', 'tanggal_setor_kampus', 'bukti_setor_kampus_path']);
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->decimal('disetor_ke_kampus', 15, 2)->nullable()->after('nominal');
            $table->date('tanggal_setor_kampus')->nullable()->after('disetor_ke_kampus');
            $table->text('bukti_setor_kampus_path')->nullable()->after('tanggal_setor_kampus');
        });
    }
};
