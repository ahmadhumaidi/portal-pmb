<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Mahasiswa extends Model
{
    use HasBusinessCode;
    use SoftDeletes;

    protected $fillable = [
        'asal_sekolah',
        'koordinator_id',
        'nama_ibu',
        'kewarganegaraan',
        'agama',
        'mahasiswa_uuid',
        'kode_pmb',
        'kampus_id',
        'jurusan_id',
        'pic_staff_id',
        'input_by',
        'nama_mahasiswa',
        'nik',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_whatsapp',
        'email',
        'alamat',
        'tahun_lulus',
        'harga_kesepakatan',
        'status_pendaftaran',
        'keterangan',
        'google_drive_folder_id',
        'google_drive_folder_url',
        'google_drive_pembayaran_folder_id',
        'google_drive_pembayaran_folder_url',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'harga_kesepakatan' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Mahasiswa $mahasiswa) {
            $mahasiswa->mahasiswa_uuid ??= (string) Str::uuid();

            if (!$mahasiswa->kode_pmb) {
                $mahasiswa->kode_pmb = static::nextBusinessCode(
                    'PMB',
                    'kode_pmb',
                    6,
                    true
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

    public function picStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'pic_staff_id');
    }

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(Koordinator::class);
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function berkas(): HasOne
    {
        return $this->hasOne(Berkas::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function setoranKampus(): HasMany
    {
        return $this->hasMany(SetoranKampus::class);
    }

    public function hasil(): HasOne
    {
        return $this->hasOne(Hasil::class);
    }

    public function resolvedBiayaKampus(): ?float
    {
        if (!$this->kampus_id) {
            return null;
        }

        $aturan = BiayaKampus::query()
            ->where('kampus_id', $this->kampus_id)
            ->where('status_aktif', true)
            ->where(function ($query) {
                $query->where('jurusan_id', $this->jurusan_id)
                    ->orWhereNull('jurusan_id');
            })
            ->orderByRaw('jurusan_id is null')
            ->first();

        if ($aturan) {
            return (float) $aturan->biaya;
        }

        return $this->kampus ? (float) $this->kampus->harga : null;
    }

    public function keuntungan(): ?float
    {
        $biayaKampus = $this->resolvedBiayaKampus();

        if ($biayaKampus === null) {
            return null;
        }

        return (float) $this->harga_kesepakatan - $biayaKampus;
    }
}


