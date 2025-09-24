<?php

namespace App\Repositories;

use App\Models\Hospital;

class HospitalRepository
{
    public function getAllHospital(array $fields)
    {
        return Hospital::select($fields)->latest()->paginate(10);
    }

    public function getHospitalById(int $id, array $fields)
    {
        return Hospital::select($fields)->with(['doctors.specialist', 'specialist'])->findOrFail($id);
    }

    public function createHospital(array $data)
    {
        return Hospital::create($data);
    }

    public function updateHospital(int $id, array $data)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->update($data);
        return $hospital;
    }

    public function deleteHospital(int $id)
    {
        $hospital = Hospital::findOrFail($id);
        return $hospital->delete();
    }
}
