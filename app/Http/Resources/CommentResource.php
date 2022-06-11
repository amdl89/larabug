<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->id, $this->id),
            'body' => $this->when($this->body, $this->body),
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),

            'user' => new UserResource($this->whenLoaded('user')),
            'ticket' => new TicketResource($this->whenLoaded('ticket')),
            'parent' => new static($this->whenLoaded('parent')),
            'replies' => static::collection($this->whenLoaded('replies')),

            'can-update' => $this->when($this->{'can-update'}, $this->{'can-update'}),
            'can-delete' => $this->when($this->{'can-delete'}, $this->{'can-delete'}),
        ];
    }
}
