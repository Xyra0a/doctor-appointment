<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SpecialistRepository;

class SpecialistService
{
    private $specialistRepository;

    public function __construct(SpecialistRepository $specialistRepository)
    {
        $this->specialistRepository = $specialistRepository;
    }

    public function getAllSpecialists(array $fields)
    {
        return $this->specialistRepository->getAllSpecialists($fields);
    }

    public function getSpecialistById(int $id, array $fields)
    {
        return $this->specialistRepository->getSpecialistById($id, $fields);
    }

    public function createSpecialist(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->specialistRepository->createSpecialist($data);
    }

    public function updateSpecialist(int $id, array $data)
    {
        $specialist = $this->specialistRepository->getSpecialistById($id, ['photo']);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Delete old photo if exists
            if ($specialist->photo) {
                $this->deletePhoto($specialist->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->specialistRepository->updateSpecialist($id, $data);
    }

    public function deleteSpecialist(int $id)
    {
        $specialist = $this->specialistRepository->getSpecialistById($id, ['photo']);
        // Delete photo if exists
        if ($specialist->photo) {
            $this->deletePhoto($specialist->photo);
        }
        return $this->specialistRepository->deleteSpecialist($id);
    }

    private function uploadPhoto(UploadedFile $photo): string //type hinting
    {
        $path = $photo->store('specialists', 'public'); // Store in 'storage/specialists/photo.png' directory
        return $path;
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'specialists/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }else{
            // Log or handle the case where the file does not exist
            // For example, you might want to log a warning
            Log::warning("Attempted to delete non-existent photo at path: " . $relativePath);
        }
    }
}
