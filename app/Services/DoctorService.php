<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Repositories\HospitalSpecialistRepository;

class DoctorService
{
    private $doctorRepository; //dependency injection
    private $hospitalSpecialistRepository;

    public function __construct(DoctorRepository $doctorRepository, HospitalSpecialistRepository $hospitalSpecialistRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }

    public function getAllDoctors(array $fields)
    {
        return $this->doctorRepository->getAllDoctors($fields);
    }

    public function getDoctorById(int $id, array $fields)
    {
        return $this->doctorRepository->getDoctorById($id, $fields);
    }

    public function createDoctor(array $data)
    {
        if(!$this->hospitalSpecialistRepository->existsForHospitalAndSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages(['specialist_id' => 'This hospital dont have this specialist']);
        }

        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->doctorRepository->createDoctor($data);
    }

    public function updateDoctor(int $id, array $data)
    {
        if(!$this->hospitalSpecialistRepository->existsForHospitalAndSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages(['specialist_id' => 'This hospital doesnt have this specialist']);
        }+

        $doctor = $this->doctorRepository->getDoctorById($id, ['photo']);
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Delete old photo if exists
            if($doctor->photo) {
                $this->deletePhoto($doctor->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->doctorRepository->updateDoctor($id, $data);
    }

    public function deleteDoctor(int $id)
    {
        $doctor = $this->doctorRepository->getDoctorById($id, ['photo']);
        if ($doctor->photo) {
            $this->deletePhoto($doctor->photo);
        }
        return $this->doctorRepository->deleteDoctor($id);
    }

    private function uploadPhoto($photo)
    {
        $path = $photo->store('doctors', 'public');
        return $path;
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'doctors/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function filterBySpecialistAndHospital(int $specialistId, int $hospitalId)
    {
        return $this->doctorRepository->filterBySpecialistAndHospital($specialistId, $hospitalId);
    }

    public function getAvailableTimes(int $doctorId) //hanya mengambil id dokter
    {
        $doctor = $this->doctorRepository->getDoctorById($doctorId, ['id']);

        $dates = collect([
            now()->addDay(1)->startOfDay('Y-m-d'),
            now()->addDays(2)->startOfDay('Y-m-d'),
            now()->addDays(3)->startOfDay('Y-m-d'),
        ]);

        $timeSlots = collect([
            '09:00', '10:00', '11:00', '13:00', '14:00', '15:00'
        ]);

        $availability = [];

        foreach($dates as $date)
        {
            $dateStr = $date->toDateString();
            $availability[$dateStr] = [];

            foreach($timeSlots as $time)
            {
                $isTaken = $doctor->bookingTransaction()
                    ->whereDate('started_at', $dateStr)
                    ->where('time_at', $time)
                    ->exists();

                if(!$isTaken) {
                    $availability[$dateStr][] = $time;
                }
            }
        }

        return $availability;
    }
}
