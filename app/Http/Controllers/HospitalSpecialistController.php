    <?php

    use App\Services\HospitalService;
    use App\Services\HospitalSpecialistService;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class HospitalSpecialistController extends Controller
    {
        //
        private $hospitalSpecialistService;

        public function __construct(HospitalSpecialistService $hospitalSpecialistService)
        {
            $this->hospitalSpecialistService = $hospitalSpecialistService;
        }

        public function attach(Request $request, int $hospitalId)
        {
            $request->validate([
                'specialist_id' => ['required', 'integer', 'exists:specialists,id'],
            ]);

            $this->hospitalSpecialistService->attachSpecialist($hospitalId, $request->input('specialist_id'));

            return response()->json(['message' => 'Specialist attached to hospital successfully'], 200);
        }

        public function detach(int $specialistId, int $hospitalId)
        {
            $this->hospitalSpecialistService->detachSpecialist($hospitalId, $specialistId);
            return response()->json(['message' => 'Specialist detached from hospital successfully'], 200);
        }
    }
