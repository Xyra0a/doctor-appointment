<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalRequest;
use App\Http\Requests\SpecialistRequest;
use Illuminate\Http\Request;
use App\Services\HospitalService;
use App\Http\Resources\HospitalResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HospitalController extends Controller
{
    //
    private $hospitalService;

    public function __construct(HospitalService $hospitalService)
    {
        $this->hospitalService = $hospitalService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'name', 'photo', 'city', 'phone'];
        $hospitals = $this->hospitalService->getAllHospital($fields);
        return response()->json(HospitalResource::collection($hospitals));
    }

    public function show(int $id)
    {
        try{
            $fields = ['*'];
            $hospital = $this->hospitalService->getHospitalById($id, $fields);
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }
    }

    public function store(HospitalRequest $request)
    {
        $data = $request->validated();
        $hospital = $this->hospitalService->createHospital($data);
        return response()->json(new HospitalResource($hospital), 201);
    }
}
