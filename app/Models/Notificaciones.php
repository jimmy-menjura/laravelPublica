<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notificaciones extends Model
{
    protected $fillable = array('message','nickname','fullname','image','typeNotify','status','user_id','to');
    
    public function Users()
    {
        return $this->belongsTo(User::class);
    }
}
