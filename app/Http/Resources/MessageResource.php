<?php

namespace App\Http\Resources;

use App\Models\UserReceivedMessage;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'subject' => $this->when($this->subject, $this->subject),
            'body' => $this->when($this->body, $this->body),
            'sentStatus' => $this->when($this->sentStatus, $this->sentStatus->key),
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),

            'sender' => new UserResource($this->whenLoaded('sender')),
            'receipents' => UserResource::collection($this->whenLoaded('receipents')),
            'receivedInfo' => new UserReceivedMessageResource(
                $this->whenLoaded('receivedMessageInfo')
            ),
        ];
    }
}
