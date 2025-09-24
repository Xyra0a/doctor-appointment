<?php

namespace App\Http\Controllers;

use App\Models\Specialist;
use Illuminate\Http\Request;
use App\Services\SpecialistService;
use App\Http\Requests\SpecialistRequest;
use App\Http\Resources\SpecialistResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SpecialistController extends Controller
{
    //
    private $specialistService;

    public function __construct(SpecialistService $specialistService)
    {
        $this->specialistService = $specialistService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'price'];
        $specialists = $this->specialistService->getAllSpecialists($fields);
        return response()->json(SpecialistResource::collection($specialists)); //KALO DATA BANYAK PAKE COLLECTION
    }

    public function show(int $id)
    {
        try{
            $fields = ['*'];
            $specialist = $this->specialistService->getSpecialistById($id, $fields);
            return response()->json(new SpecialistResource($specialist)); //KALO DATA CUMA 1 PAKE NEW
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
    }

    public function store(SpecialistRequest $request)
    {
        $data = $request->validated();
        $specialist = $this->specialistService->createSpecialist($data);
        return response()->json(new SpecialistResource($specialist), 201);
    }

    public function update(SpecialistRequest $request, int $id)
    {
        $data = $request->validated();
        try {
            $specialist = $this->specialistService->updateSpecialist($id, $data);
            return response()->json(new SpecialistResource($specialist));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->specialistService->deleteSpecialist($id);
            return response()->json(['message' => 'Specialist deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
    }

}
