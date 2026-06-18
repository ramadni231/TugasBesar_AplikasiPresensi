<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kode_matkul', 'nama_matkul', 'sks', 'semester'])]
class Matakuliah extends Model
{
    protected $table = 'matakuliah';
}
