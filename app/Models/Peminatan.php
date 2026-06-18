<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['mahasiswa_id', 'matakuliah_id', 'status'])]
class Peminatan extends Model
{
    protected $table = 'peminatan';

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'mahasiswa_id');
    }

    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class);
    }
}
