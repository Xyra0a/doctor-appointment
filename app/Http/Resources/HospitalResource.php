<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photo,
            'about' => $this->about,
            'city' => $this->city,
            'phone' => $this->phone,
            'address' => $this->address,
            'post_code' => $this->post_code,
        ];
    }
}
