<?php

namespace App\Support\Exports;

use App\Support\Exceptions\BusinessRuleException;

enum ExportFormat: string
{
    case Xlsx = 'xlsx';
    case Pdf = 'pdf';
    case Csv = 'csv';

    public static function fromInput(string $format): self
    {
        return self::tryFrom(strtolower($format)) ?? throw new BusinessRuleException(
            'Định dạng xuất không được hỗ trợ.',
            'Hãy chọn Excel (.xlsx), CSV hoặc PDF.'
        );
    }
}
