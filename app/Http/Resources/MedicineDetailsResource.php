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
        // dd($this->pharmacies);
        return [
            'id' => $this->id,
            'pharmacy_id' => $this->pharmacies[0]->id,
            'pharmacy_name' => $this->pharmacies[0]->name,
            'name' => $lang === 'bn' ? $this->name_bn : $this->name_en,
            'generic_name' => $lang === 'bn' ? $this->generic->title_bn : $this->generic->title_en,
            'concentration' => $this->concentration->value,
            'strip_price' => $this->strip_price ?? "",
            'unit_price' => $this->unit_price,
            'discounted_strip_price' => isset($this->pharmacies[0]) && !is_null($this->pharmacies[0]->pivot->discount_percentage) && !is_null($this->strip_price)
                ? round($this->strip_price * (1 - $this->pharmacies[0]->pivot->discount_percentage / 100), 2)
                : "",
            'discounted_unit_price' => isset($this->pharmacies[0]) && !is_null($this->pharmacies[0]->pivot->discount_percentage) && !is_null($this->unit_price)
                ? round($this->unit_price * (1 - $this->pharmacies[0]->pivot->discount_percentage / 100), 2)
                : "",

            'images' => $this->images->map(function ($image) {
                return asset($image->src);
            }),
            'description' => $lang === 'bn' ? $this->description_bn : $this->description_en,
            'category' => $lang === 'bn' ? $this->category->name_bn : $this->category->name_en,
            'company' => $lang === 'bn' ? $this->company->name_bn : $this->company->name_en,
            'units' => UnitResource::collection($this->whenLoaded('units'))
        ];
    }
}
