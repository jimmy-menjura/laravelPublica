<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class registro extends Model
{
    protected $table = 'registro';
    protected $fillable = array('email', 'password', 'nickName', 'fullName', 'birthdate', 'image');
}
