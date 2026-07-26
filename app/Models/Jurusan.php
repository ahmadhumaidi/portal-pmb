<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Jurusan extends Model
{
    use HasBusinessCode;

    protected $fillable = [
        'jurusan_uuid',
        'kode_jurusan',
        'kampus_id',
        'nama_jurusan',
        'jenjang',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Jurusan $jurusan) {
            $jurusan->jurusan_uuid ??= (string) Str::uuid();

            if (!$jurusan->kode_jurusan) {
                $jurusan->kode_jurusan = static::nextBusinessCode(
                    'JRS',
                    'kode_jurusan'
                );
            }
        });
    }

    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class);
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }
}

