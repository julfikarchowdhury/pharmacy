<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineDetailsResource extends JsonResource
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
            'concentration' => $this->concentration->value,
            'strip_price' => $this->strip_price ?? null,
            'unit_price' => $this->unit_price,
            'images' => $this->images->map(function ($image) {
                return asset($image->src);
            }),
            'description' => $lang === 'bn' ? $this->description_bn : $this->description_en,
            'category' => $lang === 'bn' ? $this->category->name_bn : $this->category->name_en,
            'company' => $lang === 'bn' ? $this->company->name_bn : $this->company->name_en,
            'units' => $this->units->map(function ($unit, $lang) {
                return $lang === 'bn' ? $unit->value_bn : $unit->value_en;
            }),
        ];
    }
}
