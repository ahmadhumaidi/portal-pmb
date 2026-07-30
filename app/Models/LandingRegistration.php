<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingRegistration extends Model
{
    protected $fillable = [
        'name',
        'whatsapp',
        'email',
        'school',
        'city',
        'kampus_id',
        'jurusan_id',
        'education_level',
        'notes',
        'status',
    ];

    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
