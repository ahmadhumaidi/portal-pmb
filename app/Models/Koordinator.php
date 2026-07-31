<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Koordinator extends Authenticatable
{
    use HasBusinessCode;

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'koordinator_uuid',
        'kode_koordinator',
        'nama_koordinator',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Koordinator $koordinator) {
            $koordinator->koordinator_uuid ??= (string) Str::uuid();

            if (!$koordinator->kode_koordinator) {
                $koordinator->kode_koordinator = static::nextBusinessCode(
                    'KRD',
                    'kode_koordinator'
                );
            }

            $koordinator->password ??= Hash::make('sukses1');
        });
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }
}
