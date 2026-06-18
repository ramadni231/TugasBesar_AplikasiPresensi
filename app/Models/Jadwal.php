<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['matakuliah_id', 'ruangan_id', 'dosen_id', 'hari', 'jam_mulai', 'jam_selesai'])]
class Jadwal extends Model
{
    protected $table = 'jadwal';

    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class);
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'dosen_id');
    }
}
