<?php

namespace App\Repositories;

use App\Models\Hospital;
use App\Models\HospitalSpecialist;

class HospitalSpecialistRepository
{

    public function getHospitalById(int $hospitalId, array $fields = ['*'])
    {
        return Hospital::select($fields)->findOrFail($hospitalId);
    }


    public function existsForHospitalAndSpecialist(int $hospitalId, int $specialistId): bool
    {
        return HospitalSpecialist::where('hospital_id', $hospitalId)
            ->where('specialist_id', $specialistId)
            ->exists();
    }
}
