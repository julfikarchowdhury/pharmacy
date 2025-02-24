<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PharmacyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        if (str_contains(request()->path(), 'medicine-wise-pharmacies') || str_contains(request()->path(), 'all-pharmacies')) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'logo' => $this->logo,
                'address' => $this->address,
            ];
        }

        return [
            'id' => $this->id,
            'pharmacy_owner' => new UserResource($this->whenLoaded('owner')),
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'long' => $this->long,
            'logo' => $this->logo ? asset($this->logo) : null,
            'banner' => $this->banner ? asset($this->banner) : null,
        ];
    }

}
