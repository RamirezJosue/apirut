<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this-> slug,
            'extract' => $this-> extract,
            'body' => $this->body,
            'status' => $this->status == 1 ? 'BORRADOR':'PUBLICADO',
            'user' => UserResource::make($this->whenLoaded('user')),
            'category' => CategoryResource::make($this->whenLoaded('category'))
        ];
    }
}
