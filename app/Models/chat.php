<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    use HasFactory;

    public function users(){
        return $this->belongsToMany('App\Models\User');
    }
    public function mensaje(){
        return $this->hasMany('App\Models\Mensaje');
    }
}
