<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class TagResource extends JsonResource
{

    public $preserveKeys = true;
    public static $wrap = 'tag';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cacheKey = 'tag_' . $this->id;
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $result['cached'] = true;
        } else {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'NAME_ROD' => $this->NAME_ROD,
                'NAME_DAT_ED' => $this->NAME_DAT_ED,
                'count' => $this->COUNT,
                'url' => $this->url,
                'flag' => $this->flag,
                'locations' => $this->locations,
                'children' => $this->children,
                'lat' => $this->lat,
                'lng' => $this->lng,
                'zoom' => $this->scale ? $this->scale : 7,
            ];
            Cache::put($cacheKey, $result, 3600);
        }
        return $result;
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
