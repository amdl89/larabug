<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'title' => $this->when($this->title, $this->title),
            'description' => $this->when($this->description, $this->description),
            'status' => $this->when($this->status, fn () => $this->status->key),
            'deadline' => $this->when($this->deadline, $this->deadline),
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),

            'priority' => new ProjectPriorityResource($this->whenLoaded('priority')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'devs' => UserResource::collection($this->whenLoaded('devs')),
            'testers' => UserResource::collection($this->whenLoaded('testers')),
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'coverImage' =>  $this->when(
                $this->relationLoaded('media'),
                fn () => [
                    'original' => optional($this->getFirstMedia('coverImage'))->getFullUrl(),
                    'thumbnail' => optional($this->getFirstMedia('coverImage'))->getFullUrl('coverImageThumbnail')
                ]
            ),

            'can-view' => $this->when($this->{'can-view'}, $this->{'can-view'}),
            'can-edit' => $this->when($this->{'can-edit'}, $this->{'can-edit'}),
            'can-delete' => $this->when($this->{'can-delete'}, $this->{'can-delete'}),
            'can-assign' => $this->when($this->{'can-assign'}, $this->{'can-assign'}),
        ];
    }
}
