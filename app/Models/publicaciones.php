<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class publicaciones extends Model
{
    protected $table = 'publicaciones';
    protected $fillable = array('description','image');
}
