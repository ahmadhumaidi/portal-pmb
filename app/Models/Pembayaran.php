<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasBusinessCode;

    protected $fillable = [
        'pembayaran_uuid',
        'kode_pembayaran',
        'mahasiswa_id',
        'input_by',
        'verified_by',
        'jenis_pembayaran',
        'angsuran_ke',
        'tanggal_bayar',
        'nominal',
        'status_bayar',
        'bukti_bayar_path',
        'catatan',
        'verified_at',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pembayaran $pembayaran) {
            $pembayaran->pembayaran_uuid ??= (string) Str::uuid();

            if (!$pembayaran->kode_pembayaran) {
                $pembayaran->kode_pembayaran = static::nextBusinessCode(
                    'PAY',
                    'kode_pembayaran',
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

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}



