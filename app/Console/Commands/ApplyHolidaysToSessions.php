<?php

namespace App\Console\Commands;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\Timetable\Services\SessionService;
use Illuminate\Console\Command;

/** Ngày nghỉ khai báo sau khi đã sinh buổi học: chuyển buổi Dự kiến trùng ngày nghỉ sang Nghỉ (FR-TTB-009, BR-TTB-06). */
class ApplyHolidaysToSessions extends Command
{
    protected $signature = 'timetable:apply-holidays {term? : id học kỳ; bỏ trống = mọi học kỳ chưa kết thúc}';

    protected $description = 'Chuyển buổi học trùng ngày nghỉ sang trạng thái Nghỉ';

    public function handle(SessionService $sessions): int
    {
        $terms = $this->argument('term')
            ? Term::query()->whereKey($this->argument('term'))->get()
            : Term::query()->whereNotIn('status', [TermStatus::COMPLETED, TermStatus::LOCKED])->get();

        foreach ($terms as $term) {
            $this->info("{$term->name} (#{$term->id}): ".$sessions->applyHolidays($term).' buổi chuyển sang Nghỉ.');
        }

        return self::SUCCESS;
    }
}
