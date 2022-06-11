<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'name' =>  $this->when($this->name, $this->name),
            'notes' =>  $this->when($this->notes, $this->notes),
            'attachable' => $this->when(
                $this->relationLoaded('attachable'),
                function ()
                {
                    switch ($this->attachableType)
                    {
                        case Ticket::class:
                            return new TicketResource($this->attachable);

                        case Project::class:
                            return new ProjectResource($this->attachable);
                    }
                }
            ),
            'file' =>  $this->when(
                $this->relationLoaded('media'),
                function ()
                {
                    $file = optional($this->getFirstMedia('attachedFile'));
                    return [
                        'name' => $file->name,
                        'fileName' => $file->file_name,
                        'mimeType' => $file->mime_type,
                        'size' => $file->human_readable_size,
                        'createdAt' => $file->created_at,
                    ];
                }
            ),
            'uploader' => new UserResource($this->whenLoaded('uploader')),

            'can-view' => $this->when($this->{'can-view'}, $this->{'can-view'}),
            'can-delete' => $this->when($this->{'can-delete'}, $this->{'can-delete'}),
        ];
    }
}
