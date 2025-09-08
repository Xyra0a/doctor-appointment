<?php

namespace App\Repositories;

use App\Models\Doctor;

class DoctorRepository
{
    public function getAllDoctors(array $fields)
    {
        return Doctor::select($fields)->with('specialist', 'hospital')->paginate(10);
    }

    public function getDoctorById(int $id, array $fields)
    {
        return Doctor::select($fields)->with('specialist', 'hospital', 'bookingTransaction.user')->findOrFail($id);
    }

    public function createDoctor(array $data)
    {
        return Doctor::create($data);
    }

    public function updateDoctor(int $id, array $data)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->update($data);
        return $doctor;
    }

    public function deleteDoctor(int $id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return $doctor;
    }

    public function filterBySpecialistAndHospital($specialistId, $hospitalId, array $fields)
    {
        $query = Doctor::select($fields)->with('specialist', 'hospital');

        if ($specialistId) {
            $query->where('specialist_id', $specialistId);
        }

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        return $query->get();
    }
}
