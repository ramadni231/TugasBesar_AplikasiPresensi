<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kunci', 'nilai'])]
class Pengaturan extends Model
{
    protected $table = 'pengaturan';
}
