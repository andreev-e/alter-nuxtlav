<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class UserResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->firstname . ' ' . $this->lastname,
            'username' => $this->username,
            'publications' => $this->publications,
            'about' => $this->about,
            'homepage' => $this->homepage,
        ];
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
