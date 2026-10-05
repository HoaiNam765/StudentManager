<?php

namespace App\Modules\AcademicYear\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Http\Requests\StoreAcademicYearRequest;
use App\Modules\AcademicYear\Http\Requests\StoreTermRequest;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Services\AcademicYearService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AcademicYearController extends Controller
{
    public function __construct(
        private readonly AcademicYearService $service
    ) {}

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', AcademicYear::class);

        return response()->json(
            AcademicYear::query()
                ->with('terms')
                ->orderByDesc('start_date')
                ->paginate(20)
        );
    }

    public function show(
        AcademicYear $academicYear
    ): JsonResponse {
        Gate::authorize('view', $academicYear);

        return response()->json(
            $academicYear->load('terms')
        );
    }

    public function store(
        StoreAcademicYearRequest $request
    ): JsonResponse {
        Gate::authorize('create', AcademicYear::class);

        $academicYear = $this->service->createAcademicYear(
            $request->validated()
        );

        return response()->json($academicYear, 201);
    }

    public function update(
        Request $request,
        AcademicYear $academicYear
    ): JsonResponse {
        Gate::authorize('update', $academicYear);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
        ]);

        $academicYear = $this->service->updateAcademicYear(
            $academicYear,
            $data
        );

        return response()->json($academicYear);
    }

    public function storeTerm(
        StoreTermRequest $request
    ): JsonResponse {
        Gate::authorize('create', AcademicYear::class);

        $term = $this->service->createTerm(
            $request->validated()
        );

        return response()->json($term, 201);
    }

    public function currentTerm(): JsonResponse
    {
        Gate::authorize('viewAny', AcademicYear::class);

        return response()->json(
            $this->service->getCurrentTerm()
        );
    }

    public function setCurrentTerm(
        Term $term
    ): JsonResponse {
        Gate::authorize('approve', $term);

        return response()->json(
            $this->service->setCurrentTerm($term)
        );
    }

    public function changeStatus(
        Term $term,
        TermStatus $status
    ): JsonResponse {
        Gate::authorize('approve', $term);

        return response()->json(
            $this->service->changeStatus(
                $term,
                $status
            )
        );
    }
}
