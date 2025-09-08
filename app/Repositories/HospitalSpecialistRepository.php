<?php

namespace App\Repositories;

use App\Models\HospitalSpecialist;

class HospitalSpecialistRepository
{
    public function getSpecialistsByHospitalId(int $hospitalId, array $fields)
    {
        return HospitalSpecialist::select($fields)
            ->with(['specialist' => function ($query) use ($hospitalId) {
                $query->withCount(['doctors' => function ($query) use ($hospitalId) {
                    $query->where('hospital_id', $hospitalId);
                }]);
            }])
            ->where('hospital_id', $hospitalId)
            ->get();
    }
}
