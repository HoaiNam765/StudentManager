<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Services\CourseGradeService;
use App\Modules\Grade\Services\GradebookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeResultController extends Controller
{
    public function __construct(
        private readonly CourseGradeService $grades,
        private readonly GradebookService $gradebook,
    ) {}

    public function show(
        Request $request,
        Enrollment $enrollment
    ): JsonResponse {
        // Chỉ người có quyền xem sổ điểm của lớp
        // được đọc kết quả tính.
        $this->gradebook->authorizeView(
            $enrollment->section,
            $request->user()
        );

        return response()->json([
            'enrollment_id' => $enrollment->id,
            'result' => $this->grades->calculate($enrollment),
        ]);
    }
}
