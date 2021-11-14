<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class RouteResource extends JsonResource
{
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
            'url' => Str::slug($this->name),
            'description' => htmlspecialchars_decode($this->description),
            'encoded_route' => $this->encoded_route,
            'difficilty' => $this->difficilty,
            'links' => $this->links,
            'route' => $this->route,
            'days' => $this->days,
            'cost' => $this->cost,
            'views' => $this->views,
            'START' => $this->START,
            'FINISH' => $this->FINISH,
        ];
    }
}
