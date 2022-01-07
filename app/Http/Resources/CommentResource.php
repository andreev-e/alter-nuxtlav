<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cacheKey = 'comment_' . $this->id;
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $result['cached'] = true;
        } else {
            return [
                'commentid' => $this->commentid,
                'name' => $this->name,
                'comment' => $this->comment,
                'date' => date('Y.m.d', $this->time),
                'time' => date('H:i:s', $this->time),
                'object' => $this->object,
            ];
            Cache::put($cacheKey, $result, 3600);
        }
        return $result;
    }
}
