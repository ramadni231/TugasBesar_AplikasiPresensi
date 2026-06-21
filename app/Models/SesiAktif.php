<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['jadwal_id', 'pertemuan_ke', 'token_qr', 'berakhir_pada', 'is_aktif', 'tanggal_reschedule', 'jam_mulai_reschedule', 'jam_selesai_reschedule', 'ruangan_id_reschedule'])]
class SesiAktif extends Model
{
    protected $table = 'sesi_aktif';

    protected function casts(): array
    {
        return [
            'berakhir_pada' => 'datetime',
            'is_aktif' => 'boolean',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function ruanganReschedule(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id_reschedule');
    }
}
