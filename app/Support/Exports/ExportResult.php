<?php

namespace App\Support\Exports;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

final readonly class ExportResult
{
    public function __construct(
        public int $rowCount,
        public ExportFormat $format,
        public ?BinaryFileResponse $download = null,
        public ?string $requestId = null,
    ) {}

    public function isQueued(): bool
    {
        return $this->requestId !== null;
    }
}
