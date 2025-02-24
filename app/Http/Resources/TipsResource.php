<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $lang = $request->get('lang', 'en')??'en';

        return [
            'id' => $this->id,
            'ttile' => $lang === 'bn' ? $this->title_bn : $this->title_en,
            'image' => asset($this->image),
            'video' => asset($this->video),
            'instruction' => $lang === 'bn' ? $this->instruction_bn : $this->instruction_en,
        ];
    }
}
