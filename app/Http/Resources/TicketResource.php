<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),

            'priority' =>  new TicketPriorityResource($this->whenLoaded('priority')),
            'type' => new TicketTypeResource($this->whenLoaded('type')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'assignee' => $this->when(
                $this->relationLoaded('assignee'),
                fn () => $this->assignee ? new UserResource($this->assignee) : null
            ),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'changeLogs' => ChangeLogResource::collection($this->whenLoaded('changeLogs')),

            'can-view' => $this->when($this->{'can-view'}, $this->{'can-view'}),
            'can-edit' => $this->when($this->{'can-edit'}, $this->{'can-edit'}),
            'can-delete' => $this->when($this->{'can-delete'}, $this->{'can-delete'}),
            'can-assign' => $this->when($this->{'can-assign'}, $this->{'can-assign'}),

        ];
    }
}
