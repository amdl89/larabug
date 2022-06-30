<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'name' => $this->when($this->name, $this->name),
            'title' => $this->when($this->title, $this->title),
            'bio' => $this->when($this->bio, $this->bio),
            'address' => $this->when($this->address, $this->address),
            'education' => $this->when($this->education, $this->education),
            'user' => new UserResource($this->whenLoaded('user')),
            'avatar' =>  $this->when(
                $this->relationLoaded('media'),
                fn () => [
                    'original' => optional($this->getFirstMedia('avatar'))->getUrl(),
                    'thumbnail' => optional(
                        $this->getFirstMedia('avatar')
                    )->hasGeneratedConversion('avatarThumbnail') ?
                        $this->getFirstMedia('avatar')->getFullUrl('avatarThumbnail') :
                        null,
                ]
            ),
        ];
    }
}
