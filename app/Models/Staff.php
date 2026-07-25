<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Staff extends Model
{
    use HasBusinessCode;

    protected $table = 'staff';

    protected $fillable = [
        'staff_uuid',
        'kode_staff',
        'user_id',
        'kampus_id',
        'nama_staff',
        'email',
        'role',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Staff $staff) {
            $staff->staff_uuid ??= (string) Str::uuid();

            if (!$staff->kode_staff) {
                $staff->kode_staff = static::nextBusinessCode(
                    'STF',
                    'kode_staff'
                );
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class);
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'pic_staff_id');
    }
}
