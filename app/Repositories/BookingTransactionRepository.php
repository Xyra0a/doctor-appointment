<?php

namespace App\Repositories;

use App\Models\BookingTransaction;

class BookingTransactionRepository
{
    //manager query
    public function getAll()
    {
        return BookingTransaction::with(['doctor','doctor.specialist', 'doctor.hospital', 'user'])->latest()->paginate(10);
    }

    public function getIdForManager(int $id)
    {
        return BookingTransaction::with(['doctor', 'user', 'doctor.specialist', 'doctor.hospital'])
        ->findOrFail($id);
    }

    public function updateStatus(int $id, string $status)
    {
        $transaction = BookingTransaction::findOrFail($id);
        $transaction->update(['status' => $status]);
        return $transaction;
    }
    //end manager query

    //customer query
    public function getAllForUser(int $userId)
    {
        return BookingTransaction::with(['doctor', 'user','doctor.specialist', 'doctor.hospital'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    public function getById(int $id)
    {
        return BookingTransaction::with(['doctor', 'doctor.specialist', 'doctor.hospital'])
        ->where('id', $id)
        ->where('user_id', $id)
        ->findOrFail($id);
    }

    public function isTimeSlotTakenForDoctor(int $doctorId, string $date, string $time)
    {
        return BookingTransaction::where('doctor_id', $doctorId)
            ->where('started_at', $date)
            ->where('time_at', $time)
            // ->whereIn('status', ['pending', 'confirmed']) // Only consider pending and confirmed bookings
            ->exists();
    }

    public function create(array $data)
    {
        return BookingTransaction::create($data);
    }

    public function update(int $id, array $data)
    {
        $bookingTransaction = BookingTransaction::findOrFail($id);
        $bookingTransaction->update($data);
        return $bookingTransaction;
    }

    public function delete(int $id)
    {
        $bookingTransaction = BookingTransaction::findOrFail($id);
        $bookingTransaction->delete();
        return $bookingTransaction;
    }

}
