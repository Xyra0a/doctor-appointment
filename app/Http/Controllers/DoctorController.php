<?php

namespace App\Http\Controllers;

use PhpParser\Comment\Doc;
use Illuminate\Http\Request;
use App\Services\DoctorService;
use App\Http\Requests\DoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Http\Requests\SpecialistHospitalRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DoctorController extends Controller
{
    //
    private $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'about', 'yoe', 'specialist_id', 'hospital_id', 'gender'];
        $doctors = $this->doctorService->getAllDoctors($fields);
        return response()->json(DoctorResource::collection($doctors));
    }

    public function show(int $id)
    {
        $fields = ['*'];
        try {
            $doctor = $this->doctorService->getDoctorById($id, $fields);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
    }

    public function store(DoctorRequest $request)
    {
        $data = $request->validated();
        $doctor = $this->doctorService->createDoctor($data);
        return response()->json(new DoctorResource($doctor), 201);
    }

    public function update(DoctorRequest $request, int $id)
    {
        $data = $request->validated();
        try {
            $doctor = $this->doctorService->updateDoctor($id, $data);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->doctorService->deleteDoctor($id);
            return response()->json(['message' => 'Doctor deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
    }

    public function filterBySpecialistAndHospital(SpecialistHospitalRequest $request)
    {
        $validated = $request->validated();

        $doctors = $this->doctorService->filterBySpecialistAndHospital(
            $validated['specialist_id'],
            $validated['hospital_id'],
        );
        return response()->json(DoctorResource::collection($doctors));
    }

    public function getAvailableTimes(int $doctorId)
    {
        try {
            $availableTimes = $this->doctorService->getAvailableTimes($doctorId);
            return response()->json(['data' => $availableTimes]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
    }

    // public function filterBySpecialist(Request $request)
    // {
    //     $validated = $request->validate([
    //         'specialist_id' => ['required', 'integer', 'exists:specialists,id'],
    //         'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
    //     ]);

    //     $doctors = $this->doctorService->filterBySpecialistAndHospital(
    //         $validated['specialist_id'],
    //         $validated['hospital_id'],
    //     );
    //     return response()->json(DoctorResource::collection($doctors));
    // }

}
