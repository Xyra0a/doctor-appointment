<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Repositories\DoctorRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\BookingTransactionRepository;

class BookingTransactionService
{
    private $bookingTransactionRepository;
    private $doctorRepository;

    public function __construct(BookingTransactionRepository $bookingTransactionRepository, DoctorRepository $doctorRepository)
    {
        $this->bookingTransactionRepository = $bookingTransactionRepository;
        $this->doctorRepository = $doctorRepository;
    }

    // manager services
    public function getAll(array $fields)
    {
        return $this->bookingTransactionRepository->getAll($fields);
    }

    public function getIdForManager(int $id, array $fields)
    {
        return $this->bookingTransactionRepository->getIdForManager($id, $fields);
    }

    public function updateStatus(int $id, array $data)
    {
        if(!in_array($data['status'], ['Approved', 'Rejected'])) {
            throw ValidationException::withMessages(['status' => 'Invalid status value. Allowed values are: Approved, Rejected.']);
        }
    }

    // user services
    public function getAllForUser(int $userId)
    {
        return $this->bookingTransactionRepository->getAllForUser($userId);
    }

    public function getById(int $id, int $userId)
    {
        return $this->bookingTransactionRepository->getById($id, $userId);
    }

    public function create(array $data)
    {
        $data['user_id'] = auth()->id();
        if($this->bookingTransactionRepository->isTimeSlotTakenForDoctor($data['doctor_id'], $data['started_at'], $data['time_at'])) {
            throw ValidationException::withMessages(['time_at' => 'This time slot is already taken for the selected doctor on the chosen date. Please select a different time or date.']);
        }

        $doctor = $this->doctorRepository->getDoctorById($data['doctor_id'], ['*']);

        $price = $doctor->specialist->price ?? 0;
        $tax = $price * 0.11; // 10% tax
        $total = $price + $tax;

        $data['sub_total'] = $price;
        $data['tax_total'] = $tax;
        $data['grand_total'] = $total;
        $data['status'] = 'Waiting';

        if(isset($data['proof']) && $data['proof'] instanceof UploadedFile) {
            $data['proof'] = $this->uploadProof($data['proof']);
        }

    }

    private function uploadProof(UploadedFile $file)
    {
        return $file->store('proofs', 'public');
    }
}
