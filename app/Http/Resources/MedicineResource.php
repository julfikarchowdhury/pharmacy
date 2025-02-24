<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $lang = $request->get('lang', 'en');
        return [
            'id' => $this->id,
            'name' => $lang === 'bn' ? $this->name_bn : $this->name_en,
            'generic_name' => $lang === 'bn' ? $this->generic->title_bn : $this->generic->title_en,
            'strip_price' => $this->strip_price ?? null,
            'unit_price' => $this->unit_price,
            'image' => $this->medicineThumb ? asset($this->medicineThumb[0]->src) : null,
        ];
    }
}
