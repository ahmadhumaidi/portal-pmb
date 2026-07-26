<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BiayaKampus extends Model
{
    use HasBusinessCode;

    protected $table = 'biaya_kampus';

    protected $fillable = [
        'biaya_kampus_uuid',
        'kode_biaya_kampus',
        'kampus_id',
        'jurusan_id',
        'biaya',
        'keterangan',
        'status_aktif',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (BiayaKampus $biayaKampus) {
            $biayaKampus->biaya_kampus_uuid ??= (string) Str::uuid();

            if (!$biayaKampus->kode_biaya_kampus) {
                $biayaKampus->kode_biaya_kampus = static::nextBusinessCode(
                    'BYK',
                    'kode_biaya_kampus'
                );
            }
        });
    }

    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
