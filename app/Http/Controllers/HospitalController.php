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

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'city', 'phone'];
        $hospitals = $this->hospitalService->getAllHospital($fields);
        return response()->json(HospitalResource::collection($hospitals));
    }
    // perbedaan menggunakan collection dan new
    // collection digunakan untuk mengembalikan banyak data [ {data 1}, {data 2} ]
    // sedangkan new digunakan untuk mengembalikan satu data [ {data 1} ]

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


    public function update(HospitalRequest $request, int $id)
    {
        $data = $request->validated();
        try{
            $hospital = $this->hospitalService->updateHospital($id, $data);
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }
    }

    public function destroy(int $id)
    {
        try{
            $this->hospitalService->deleteHospital($id);
            return response()->json(['message' => 'Hospital deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }
    }
}
