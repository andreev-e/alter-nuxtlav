<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Poi extends Model
{
    protected $table = 'poi';

    protected $fillable = [
        'description',
        'route',
        'route_o',
        'lat',
        'lng',
        'name',
        'type',
        'copyright',
        'addon'
    ];
    
    protected $hidden = [
        'near',
        'metki',
    ];

    public function getImagesAttribute() 
    {
        return $this->images()->select('id', 'path', 'description')->get();
    }

    public function getNearestAttribute() 
    {
        $nearest = self::select(
            '*',
            DB::raw("SQRT(POW((`poi`.`lat` - " . $this->lat . "), 2) + POW((`poi`.`lng` - " . $this->lng . "), 2)) as dist"),
            )
            ->where('id', '<>', $this->id)
            ->orderBy('dist', 'asc')
            ->limit(4)
            ->get();
        return $nearest;
    }

    public function getNearesttagsAttribute() 
    {
        $nearest = [];
        $tags = $this->tags()->get();
        foreach ($tags as $tag) {
            $nearest[$tag->name] = self::select(
                '*',
                DB::raw("SQRT(POW((`poi`.`lat` - " . $this->lat . "), 2) + POW((`poi`.`lng` - " . $this->lng . "), 2)) as dist"),
                )->where('id', '<>', $this->id)
                ->orderBy('dist', 'asc')
                ->limit(4)
                ->whereHas('tags', function ($query) use ($tag) {
                    return $query->where('id', '=', $tag->id);
                })
                ->get();
                //todo tag query
        }
        return $nearest ;
    }

    public function tags() 
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')->where('TYPE', 0);
    }
    public function locations() 
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')->where('TYPE', '<>', 0)->orderBy('COUNT', 'desc');
    }
    public function twits() 
    {
        return $this->hasMany(Comment::class, 'backlink')->where('approved', '=', 1)->orderBy('time', 'desc');
    }
    public function images() 
    {
        return $this->hasMany(Image::class, 'parent');
    }

}
