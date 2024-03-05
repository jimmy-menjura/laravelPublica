<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class nosendnotifies extends Model
{
    protected $fillable = ['user_send', 'user_id'];

    public function Users()
    {
        return $this->belongsTo(User::class);
    }
}
