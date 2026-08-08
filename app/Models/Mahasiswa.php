<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessCode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Mahasiswa extends Authenticatable
{
    use HasBusinessCode;
    use SoftDeletes;

    protected $hidden = [
        'password',
    ];

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

            $mahasiswa->password ??= Hash::make('ukcw2026');
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

    private bool $aturanBiayaKampusResolved = false;

    private ?\Illuminate\Support\Collection $aturanBiayaKampusCache = null;

    /**
     * All active Biaya Kampus rules that apply to this mahasiswa, ordered with the
     * jurusan-specific rule first and the "Semua Prodi" (jurusan_id null) rule last.
     */
    private function resolvedAturanBiayaKampusCollection(): \Illuminate\Support\Collection
    {
        if ($this->aturanBiayaKampusResolved) {
            return $this->aturanBiayaKampusCache;
        }

        $this->aturanBiayaKampusResolved = true;

        if (!$this->kampus_id) {
            return $this->aturanBiayaKampusCache = collect();
        }

        return $this->aturanBiayaKampusCache = BiayaKampus::query()
            ->where('kampus_id', $this->kampus_id)
            ->where('status_aktif', true)
            ->where(function ($query) {
                $query->where('jurusan_id', $this->jurusan_id)
                    ->orWhereNull('jurusan_id');
            })
            ->orderByRaw('jurusan_id is null')
            ->get();
    }

    public function resolvedAturanBiayaKampus(): ?BiayaKampus
    {
        return $this->resolvedAturanBiayaKampusCollection()->first();
    }

    public function resolvedBiayaKampus(): ?float
    {
        $biaya = $this->resolvedBiayaKampusField('biaya');

        if ($biaya !== null && $biaya > 0) {
            return $biaya;
        }

        return $this->kampus ? (float) $this->kampus->harga : $biaya;
    }

    /**
     * Resolve a fee column (e.g. biaya_wisuda) from the jurusan-specific rule first,
     * falling back to the "Semua Prodi" rule when the specific rule has it at 0 —
     * this lets Wisuda/Almamater be set once for all prodi even when each prodi has
     * its own tuition rule.
     */
    private function resolvedBiayaKampusField(string $column): ?float
    {
        $aturans = $this->resolvedAturanBiayaKampusCollection();

        if ($aturans->isEmpty()) {
            return null;
        }

        $withValue = $aturans->first(fn (BiayaKampus $aturan) => (float) $aturan->{$column} > 0);

        return (float) ($withValue ?? $aturans->first())->{$column};
    }

    public function resolvedBiayaWisuda(): ?float
    {
        return $this->resolvedBiayaKampusField('biaya_wisuda');
    }

    public function resolvedBiayaAlmamater(): ?float
    {
        return $this->resolvedBiayaKampusField('biaya_almamater');
    }

    public function keuntungan(): ?float
    {
        $biayaKampus = $this->resolvedBiayaKampus();

        if ($biayaKampus === null) {
            return null;
        }

        return (float) $this->harga_kesepakatan - $biayaKampus;
    }

    public function totalDibayarMahasiswa(): float
    {
        return (float) $this->pembayarans
            ->where('jenis_pembayaran', 'Angsuran')
            ->where('status_bayar', 'terverifikasi')
            ->sum('nominal');
    }

    public function totalTagihan(): float
    {
        return (float) $this->harga_kesepakatan - $this->totalDibayarMahasiswa();
    }

    public function sudahLunas(): bool
    {
        return $this->totalTagihan() <= 0;
    }

    public function statusPembayaranJenis(string $jenis): string
    {
        $pembayarans = $this->pembayarans->where('jenis_pembayaran', $jenis);

        if ($pembayarans->where('status_bayar', 'terverifikasi')->isNotEmpty()) {
            return 'Lunas';
        }

        if ($pembayarans->where('status_bayar', 'menunggu')->isNotEmpty()) {
            return 'Menunggu Verifikasi';
        }

        return 'Belum Bayar';
    }

    public function statusBiayaPendidikan(): string
    {
        if ($this->sudahLunas()) {
            return 'Lunas';
        }

        $adaMenunggu = $this->pembayarans
            ->where('jenis_pembayaran', 'Angsuran')
            ->where('status_bayar', 'menunggu')
            ->isNotEmpty();

        return $adaMenunggu ? 'Menunggu Verifikasi' : 'Belum Lunas';
    }

    public function totalSetorKampusJenis(string $jenis): float
    {
        return (float) $this->setoranKampus
            ->where('jenis_setoran', $jenis)
            ->sum('nominal');
    }

    public function totalSetorKampus(): float
    {
        return $this->totalSetorKampusJenis('Biaya Pendidikan');
    }

    public function targetBiayaKampusJenis(string $jenis): ?float
    {
        return match ($jenis) {
            'Wisuda' => $this->resolvedBiayaWisuda(),
            'Almamater' => $this->resolvedBiayaAlmamater(),
            default => $this->resolvedBiayaKampus(),
        };
    }

    /**
     * Almamater is an optional add-on — students may or may not buy one. A mahasiswa
     * only "opts in" once they have an actual Almamater payment record, so campus
     * obligation for it shouldn't be treated as arrears until then.
     */
    public function sudahOptInAlmamater(): bool
    {
        return $this->pembayarans->where('jenis_pembayaran', 'Almamater')->isNotEmpty();
    }

    public function kewajibanKampusJenis(string $jenis): ?float
    {
        if ($jenis === 'Almamater' && !$this->sudahOptInAlmamater()) {
            return null;
        }

        $target = $this->targetBiayaKampusJenis($jenis);

        if ($target === null) {
            return null;
        }

        return $target - $this->totalSetorKampusJenis($jenis);
    }

    public function kewajibanKampus(): ?float
    {
        return $this->kewajibanKampusJenis('Biaya Pendidikan');
    }
}


