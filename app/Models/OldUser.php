<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldUser extends Model
{
    protected $table = "users";
    protected $primaryKey = 'username';  
    protected $keyType = 'string';  

    protected $hidden = [
        'password',
    ];
}
