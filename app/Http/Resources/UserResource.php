<?php

namespace App\Http\Resources;

use App\Models\ChangeLog;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->when($this->username, $this->username),
            'email' => $this->when($this->email, $this->email),
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),

            'role' => $this->when(
                $this->relationLoaded('roles'),
                fn () => $this->roles->pluck('name')->first()
            ),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'sentMessages' => MessageResource::collection($this->whenLoaded('sentMessages')),
            'changeLogs' => ChangeLogResource::collection($this->whenLoaded('changeLogs')),
            'receivedMessages' => MessageResource::collection($this->whenLoaded('receivedMessages')),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
        ];
    }
}
