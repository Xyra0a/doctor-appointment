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
            // kenapa ini ga pake parent::toArray($request) ???
            // biar bisa custom sendiri outputnya
            // kalo pake parent, outputnya semua field di table
            // jadi kalau yang di doctor pake parrent karna dia engga butuh custom output
            // tapi kalo di hospital butuh custom output

            'doctors_count' => $this->doctors->count(),
            'specialists_count' => $this->specialists->count(),

            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'specialists' => SpecialistResource::collection($this->whenLoaded('specialists')),
        ];
    }
}
