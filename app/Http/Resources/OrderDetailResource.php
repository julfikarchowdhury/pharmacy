<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'medicine' => $this->medicine->name_en,
            'unit' => $this->unit->value_en,
            'concentration' => $this->medicine->concentration->value,
            'qty' => $this->qty,
            'price' => $this->price,
            'discounted_price' => $this->discounted_price,
            'status' => $this->status,
        ];
    }
}
