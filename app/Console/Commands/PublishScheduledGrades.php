<?php

namespace App\Console\Commands;

use App\Modules\Grade\Services\GradeWorkflowService;
use Illuminate\Console\Command;

/** Công bố các bảng điểm đã hẹn giờ và tới giờ (FR-GRD-005). */
class PublishScheduledGrades extends Command
{
    protected $signature = 'grades:publish-scheduled';

    protected $description = 'Công bố bảng điểm đã hẹn giờ khi tới giờ';

    public function handle(GradeWorkflowService $workflow): int
    {
        $this->info($workflow->publishScheduled().' lớp đã công bố điểm.');

        return self::SUCCESS;
    }
}
