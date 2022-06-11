<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->resource->load(['profile.media', 'roles']);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'role' => $this->roles->pluck('name')->first(),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
        ];
    }
}
