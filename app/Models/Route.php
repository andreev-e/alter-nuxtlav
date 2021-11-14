<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes';

    // public function tags() 
    // {
    //     return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')->where('TYPE', 0);
    // }

    // public function twits() 
    // {
    //     return $this->hasMany(Comment::class, 'backlink')->where('approved', '=', 1)->orderBy('time', 'desc');
    // }

}
