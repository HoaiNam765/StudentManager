<?php

namespace App\Modules\Grade\Events;

use Illuminate\Foundation\Events\Dispatchable;

/** EV-GRD-02: event carries IDs only; notification module resolves recipient. */
class GradeAdjusted
{
    use Dispatchable;

    public function __construct(public readonly int $enrollmentId, public readonly int $changeRequestId) {}
}
