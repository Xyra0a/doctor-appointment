<?php

namespace App\Services;

use App\Repositories\HospitalRepository;
use App\Repositories\HospitalSpecialistRepository;

class HospitalSpecialistService
{
      // Specialist Management
    private $hospitalSpecialistRepository;

    public function __construct(HospitalSpecialistRepository $hospitalSpecialistRepository)
    {
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }

    public function attachSpecialist(int $hospitalId, int $specialistIds)
    {
        $hospital = $this->hospitalSpecialistRepository->getHospitalById($hospitalId, ['id']);
        $hospital->specialists()->syncWithoutDetaching($specialistIds);
        return $hospital->load('specialists');
    }

    // Detach a specialist from a hospital
    public function detachSpecialist(int $hospitalId, int $specialistIds)
    {
        $hospital = $this->hospitalSpecialistRepository->getHospitalById($hospitalId, ['id']);
        $hospital->specialists()->detach($specialistIds); // hapus id tertentu dari relasi karna $specialistIds merupakan parameter yang dibuat untuk menampung id
        return $hospital->load('specialists');
    }
}
