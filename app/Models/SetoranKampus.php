<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SetoranKampus extends Model
{
    use HasBusinessCode;

    protected $table = 'setoran_kampus';

    protected $fillable = [
        'setoran_kampus_uuid',
        'kode_setoran_kampus',
        'mahasiswa_id',
        'input_by',
        'nominal',
        'tanggal_setor',
        'bukti_setor_path',
        'catatan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_setor' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (SetoranKampus $setoranKampus) {
            $setoranKampus->setoran_kampus_uuid ??= (string) Str::uuid();

            if (!$setoranKampus->kode_setoran_kampus) {
                $setoranKampus->kode_setoran_kampus = static::nextBusinessCode(
                    'STK',
                    'kode_setoran_kampus',
                    6,
                    true
                );
            }
        });
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
