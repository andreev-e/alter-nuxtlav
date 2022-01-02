<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PoiResourceNear extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cacheKey = 'poi_near_' . $this->id;
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
                'dist' => $this->dist,
            ];
            Cache::put($cacheKey, $result, 3600);
        }
        return $result;
    }
}
