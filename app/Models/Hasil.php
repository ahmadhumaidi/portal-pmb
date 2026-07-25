<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Hasil extends Model
{
    use HasBusinessCode;

    protected $fillable = [
        'status_kelulusan',
        'hasil_uuid',
        'kode_hasil',
        'mahasiswa_id',
        'input_by',
        'nim',
        'nomor_seri_ijazah',
        'link_pddikti',
        'screenshot_pisn_path',
        'screenshot_satudikti_path',
        'scan_ijazah_path',
        'scan_transkrip_path',
        'status_kirim',
        'tanggal_kirim',
        'metode_kirim',
        'nomor_resi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Hasil $hasil) {
            $hasil->hasil_uuid ??= (string) Str::uuid();

            if (!$hasil->kode_hasil) {
                $hasil->kode_hasil = static::nextBusinessCode(
                    'HSL',
                    'kode_hasil',
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



