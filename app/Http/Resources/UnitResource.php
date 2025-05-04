<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = $request->get('lang', 'en') ?? 'en';

        return [
            'id' => $this->id,
            'value' => $lang === 'bn' ? $this->value_bn : $this->value_en,
        ];
    }
}
