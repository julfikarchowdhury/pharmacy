<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_id' => $this->tracking_id,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'pharmacy' => new UserResource($this->whenLoaded('pharmacy')),
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'status' => $this->status,
            'order_details' => OrderDetailResource::collection($this->whenLoaded('orderDetails')),
        ];
    }
}
