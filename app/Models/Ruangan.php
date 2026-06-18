<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama_ruangan', 'kapasitas', 'latitude', 'longitude', 'radius_meter'])]
class Ruangan extends Model
{
    protected $table = 'ruangan';
}
