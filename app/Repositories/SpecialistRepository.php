<?php

namespace App\Repositories;

use App\Models\Specialist;

class SpecialistRepository
{
    public function getAllSpecialists(array $fields)
    {
        return Specialist::select($fields)->with(['hospitals', 'doctors'])->get();
    }

    public function getSpecialistById(int $id, array $fields)
    {
        return Specialist::select($fields)
            ->with(['hospitals' => function ($query) use ($id){
                $query->withCount(['doctors' => function ($query) use ($id){
                    $query->where('specialist_id', $id);
                }]);
            },
            'doctors' => function ($query) use ($id){
                $query->where('specialist_id', $id)->with('hospital:id,name,city,post_code');
            }])
            ->findOrFail($id);
    }

    // CRUD Operations
    public function createSpecialist(array $data)
    {
        return Specialist::create($data);
    }

    public function updateSpecialist(int $id, array $data)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->update($data);
        return $specialist;
    }

    public function deleteSpecialist(int $id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->delete();
        return $specialist;
    }

    public function getSpecialistsWithDoctorCount(int $hospitalId)
    {
        return Specialist::withCount(['doctors' => function ($q) use ($hospitalId) {
            $q->where('hospital_id', $hospitalId);
        }])->get();
    }



}
