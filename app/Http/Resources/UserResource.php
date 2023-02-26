<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'    => $this->id,
            'name'  => $this->name,
        
            $this->mergeWhen($user->id == $this->id, [
                'email'           => $this->email,
                'total_followers' => $this->total_followers ?? 0,
                'total_follows'   => $this->total_follows ?? 0
            ]),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
