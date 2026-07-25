<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berkas extends Model
{
    use HasBusinessCode;

    protected $table = 'berkas';

    protected $fillable = [
        'berkas_uuid',
        'kode_berkas',
        'mahasiswa_id',
        'input_by',
        'ijazah_path',
        'transkrip_awal_path',
        'ktp_path',
        'kk_path',
        'pas_foto_path',
        'status_verifikasi',
        'keterangan',
    ];

    protected $casts = [
    ];

    protected static function booted(): void
    {
        static::creating(function (Berkas $berkas) {
            $berkas->berkas_uuid ??= (string) Str::uuid();

            if (!$berkas->kode_berkas) {
                $berkas->kode_berkas = static::nextBusinessCode(
                    'DOC',
                    'kode_berkas',
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



