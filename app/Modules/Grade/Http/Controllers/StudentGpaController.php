<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Grade\Services\GpaService;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentGpaController extends Controller
{
    public function __construct(private readonly GpaService $gpaService) {}

    public function mine(Request $request): JsonResponse
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();
        $this->gpaService->recalculate($student);

        return response()->json([
            'terms' => TermGpa::where('student_id', $student->id)->orderBy('term_id')->get(),
            'cumulative' => CumulativeGpa::where('student_id', $student->id)->first(),
        ]);
    }
}
