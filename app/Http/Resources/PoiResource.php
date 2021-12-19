<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PoiResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cacheKey = 'poi_' . $this->id;
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $result['cached'] = true;
        } else {
            $result =  [
                'id' => $this->id,
                'name' => $this->name,
                'lat' => $this->lat,
                'lng' => $this->lng,
                'url' => Str::slug($this->name),
                'ytb' => $this->ytb,
                'author' => $this->author,
                'type' => $this->type,
                'views' => $this->views,
            ];
            $result['nearesttags'] = $this->nearesttags;
            $result['nearesttype'] = $this->nearesttype;
            $result['nearest'] = $this->nearest;
            $result['images'] = $this->images;
            $result['locations'] = array_merge(
                $this->locations->toArray(),
                [['id' => $this->id, 'name' => $this->name, 'url' => null]] // adding last breadcrumb without link
            );
            $result['description'] = $this->description;
            $result['route'] = $this->route;
            $result['route_o'] = $this->route_o;
            $result['comments'] = $this->comments;
            $result['tags'] = $this->tags->pluck('id');
            $result['addon'] = $this->addon;
            $result['copyright'] = $this->copyright;
            $result['links'] = $this->links;
            Cache::put($cacheKey, $result, 3600);
        }
        return $result;
    }
}
