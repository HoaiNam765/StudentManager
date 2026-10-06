<?php

namespace App\Modules\AcademicYear\Http\Requests;

/**
 * Sửa năm học dùng cùng quy tắc kiểm tra và thông báo lỗi với tạo mới.
 * Việc phân quyền do Gate::authorize('update', ...) trong controller đảm nhiệm.
 */
class UpdateAcademicYearRequest extends StoreAcademicYearRequest {}
