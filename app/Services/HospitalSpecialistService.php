<?php

namespace App\Services;

class HospitalSpecialistService
{
      // Specialist Management
    private $hospitalRepository;

    public function __construct($hospitalRepository)
    {
        $this->hospitalRepository = $hospitalRepository;
    }

    public function attachSpecialist(int $hospitalId, int $specialistIds)
    {
        $hospital = $this->hospitalRepository->getHospitalById($hospitalId, ['id']);
        $hospital->specialist()->syncWithoutDetaching($specialistIds); //nambah tanpa menghapus yang lama, defaultnya sync itu hapus yang lama
        return $hospital->load('specialist');
    }

    // Detach a specialist from a hospital
    public function detachSpecialist(int $hospitalId, int $specialistIds)
    {
        $hospital = $this->hospitalRepository->getHospitalById($hospitalId, ['id']);
        $hospital->specialist()->detach($specialistIds); // hapus id tertentu dari relasi karna $specialistIds merupakan parameter yang dibuat untuk menampung id
        return $hospital->load('specialist');
    }
}
