<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'isi',
        'tanggal',
        'status_aktif',
        'lampiran_path',
        'input_by',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'tanggal' => 'date',
    ];

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
