<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PoiResourceLight;

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
        'addon',
        'links'
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
        $nearest = PoiResourceLight::collection(self::select(
            '*',
            DB::raw("ROUND (6371 *
            acos(
                cos(radians(" . $this->lat . "))
             * cos(radians(lat))
             *cos(radians(lng) - radians(" . $this->lng . "))
             + sin(radians(" . $this->lat . "))
             * sin(radians(lat)))
                   , 2) as dist"),
            )
            ->where('id', '<>', $this->id)
            ->orderBy('dist', 'asc')
            ->limit(4)
            ->get());
        return $nearest;
    }

    public function getNearesttypeAttribute() 
    {
        $nearest = PoiResourceLight::collection(self::select(
            '*',
            DB::raw("ROUND (6371 *
            acos(
                cos(radians(" . $this->lat . "))
             * cos(radians(lat))
             *cos(radians(lng) - radians(" . $this->lng . "))
             + sin(radians(" . $this->lat . "))
             * sin(radians(lat)))
                , 2) as dist"),
            )
            ->where('id', '<>', $this->id)
            ->where('type', '=', $this->type)
            ->orderBy('dist', 'asc')
            ->limit(4)
            ->get());
        return $nearest;
    }

    public function getNearesttagsAttribute() 
    {
        $nearest = [];
        $tags = $this->tags()->get();
        foreach ($tags as $tag) {
            $nearest[$tag->name] = PoiResourceLight::collection(self::select(
                '*',
                DB::raw("ROUND (6371 *
                acos(
                    cos(radians(" . $this->lat . "))
                 * cos(radians(lat))
                 *cos(radians(lng) - radians(" . $this->lng . "))
                 + sin(radians(" . $this->lat . "))
                 * sin(radians(lat)))
                    , 2) as dist"),
                )->where('id', '<>', $this->id)
                ->orderBy('dist', 'asc')
                ->limit(4)
                ->whereHas('tags', function ($query) use ($tag) {
                    return $query->where('id', '=', $tag->id);
                })
                ->get());
        }
        return $nearest;
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

    public function getMainImageAttribute() 
    {
        $main = $this->images()->where('is_main', 1)->first();
        if ($main) {
            return $main->path;
        } else {
            return 'empty.jpg';
        }
         
    }

}
