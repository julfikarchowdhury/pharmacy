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
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'lat' => (float) $this->lat,
            'long' => (float) $this->long,
            'status' => $this->status ?? 'active',
            'image' => asset($this->image),
            'points' => $this->points ?? 0,
            'useable_amount' => setting()->points_conversion * ($this->points ?? 0),
            'pharmacy' => new PharmacyResource($this->whenLoaded('pharmacy')),

        ];
    }
}
