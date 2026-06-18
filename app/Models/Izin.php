<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengguna_id', 'tipe_izin', 'tanggal', 'alasan', 'jalur_lampiran', 'status_persetujuan', 'disetujui_oleh'])]
class Izin extends Model
{
    protected $table = 'izin';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'disetujui_oleh');
    }
}
