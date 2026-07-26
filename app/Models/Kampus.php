<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kampus extends Model
{
    use HasBusinessCode;

    protected $fillable = [
        'kampus_uuid',
        'kode_kampus',
        'nama_kampus',
        'harga',
        'catatan',
        'status_aktif',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Kampus $kampus) {
            $kampus->kampus_uuid ??= (string) Str::uuid();

            if (!$kampus->kode_kampus) {
                $kampus->kode_kampus = static::nextBusinessCode(
                    'KMP',
                    'kode_kampus'
                );
            }
        });
    }

    public function jurusans(): HasMany
    {
        return $this->hasMany(Jurusan::class);
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }
}

