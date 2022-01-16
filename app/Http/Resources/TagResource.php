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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'NAME_ROD' => $this->NAME_ROD,
            'NAME_DAT_ED' => $this->NAME_DAT_ED,
            'NAME_ROD_ED' => $this->NAME_ROD_ED,
            'NAME_PREDLOZH_ED' => $this->NAME_PREDLOZH_ED,
            'count' => $this->COUNT,
            'url' => $this->url,
            'flag' => $this->flag,
            'locations' => $this->locations,
            'children' => $this->children,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'zoom' => $this->scale ? $this->scale : 7,
        ];
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
