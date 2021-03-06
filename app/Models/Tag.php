<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{

    protected $hidden = array('lat', 'lng', 'scale');

    protected $fillable = ['url'];

    public $timestamps = false;

    public function getUrlAttribute($url)
    {
        if ($this->TYPE !== 0) {
            return '/region/' . $url;  
        } else {
            return '/tag/' . $url;  
        }
    }

    public function pois() 
    {
        return $this->belongsToMany(Poi::class, 'relationship', 'TAGID', 'POSTID');
    }

    public function getParents(Array $parents = []) : array
    {
        if ($this->parent !== 0) {
            $parent = self::find($this->parent);
            if (is_object($parent)) {
                // $parents[] = ['id' => $parent->id, 'name' => $parent->name, 'url' => $parent->url];
                array_unshift($parents, ['id' => $parent->id, 'name' => $parent->name, 'url' => $parent->url]);
                if ($parent->parent !== 0) {
                    if (count($parents)>5) dd();
                    $parents = $parent->getParents($parents);
                }
            }
        }
        return $parents;
    }

    public function getChildren() : array
    {
        $children = self::select('id', 'name', 'url')
            ->where('parent', '=', $this->id)
            ->where('type', '<>', 0)
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
        return $children;
    }


}
