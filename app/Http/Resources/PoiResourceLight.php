<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PoiResourceLight extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
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
            'main_image' => $this->main_image,
            'dominatecolor' => $this->dominatecolor,
            'comments' => $this->comments,
        ];
        return $result;
    }
}
