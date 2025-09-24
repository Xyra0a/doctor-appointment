<?php

namespace App\Services;

use App\Repositories\HospitalRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HospitalService
{
    private $hospitalRepository;
    public function __construct(HospitalRepository $hospitalRepository)
    {
        $this->hospitalRepository = $hospitalRepository;
    }

    public function getAllHospitals(array $fields)
    {
        return $this->hospitalRepository->getAllHospitals($fields);
    }

    public function getHospitalById(int $id, array $fields)
    {
        return $this->hospitalRepository->getHospitalById($id, $fields);
    }


    public function createHospital(array $data)
    {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->hospitalRepository->createHospital($data);
    }

    public function updateHospital(int $id, array $data)
    {
        $hospital = $this->hospitalRepository->getHospitalById($id, ['photo']);
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Delete old photo if exists
            if($hospital->photo) {
                $this->deletePhoto($hospital->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->hospitalRepository->updateHospital($id, $data);
    }

    public function deleteHospital(int $id)
    {
        $hospital = $this->hospitalRepository->getHospitalById($id, ['photo']);
        // Delete photo if exists
        if($hospital->photo) {
            $this->deletePhoto($hospital->photo);
        }
        return $this->hospitalRepository->deleteHospital($id);
    }

    private function uploadPhoto($photo)
    {
        $path = $photo->store('hospitals', 'public');
        return $path;
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'hospitals/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
