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
            'customer_name' => $this->customer->name,
            'customer_phone' => $this->customer->phone,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'status' => $this->status,
            'order_details' => OrderDetailResource::collection($this->whenLoaded('orderDetails')),

        ];
    }
}
