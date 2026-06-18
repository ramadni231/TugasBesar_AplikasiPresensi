<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['jadwal_id', 'pertemuan_ke', 'mahasiswa_id', 'tanggal', 'jam_masuk', 'status', 'lat_scan', 'lng_scan'])]
class Presensi extends Model
{
    protected $table = 'presensi';

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'mahasiswa_id');
    }
}
