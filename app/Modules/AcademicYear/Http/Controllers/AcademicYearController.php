<?php

namespace App\Modules\AcademicYear\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Http\Requests\StoreAcademicYearRequest;
use App\Modules\AcademicYear\Http\Requests\StoreTermRequest;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Services\AcademicYearService;
use Illuminate\Http\JsonResponse;

class AcademicYearController extends Controller
{
    public function __construct(
        private readonly AcademicYearService $service
    ) {}

    public function store(
        StoreAcademicYearRequest $request
    ): JsonResponse {
        $academicYear = $this->service->createAcademicYear(
            $request->validated()
        );

        return response()->json($academicYear, 201);
    }

    public function storeTerm(
        StoreTermRequest $request
    ): JsonResponse {
        $term = $this->service->createTerm(
            $request->validated()
        );

        return response()->json($term, 201);
    }

    public function currentTerm(): JsonResponse
    {
        return response()->json(
            $this->service->getCurrentTerm()
        );
    }

    public function setCurrentTerm(Term $term): JsonResponse
    {
        return response()->json(
            $this->service->setCurrentTerm($term)
        );
    }

    public function changeStatus(
        Term $term,
        TermStatus $status
    ): JsonResponse {
        return response()->json(
            $this->service->changeStatus($term, $status)
        );
    }
}
