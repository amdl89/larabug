<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChangeLogResource extends JsonResource
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
            'data' => $this->when($this->data, $this->data),
            'resolvedData' => $this->when($this->resolvedData, $this->resolvedData),
            'date' => $this->when($this->date, $this->date),
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),

            'initiator' => $this->when(
                $this->relationLoaded('initiator'),
                fn () => $this->initiator ? new UserResource($this->initiator) : null
            ),
        ];
    }
}
