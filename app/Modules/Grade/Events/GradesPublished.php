<?php

namespace App\Modules\Grade\Events;

use Illuminate\Foundation\Events\Dispatchable;

/** EV-GRD-01: event carries IDs only; notification module resolves recipient. */
class GradesPublished
{
    use Dispatchable;

    public function __construct(public readonly int $courseSectionId, public readonly int $actorId) {}
}
