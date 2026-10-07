<?php

namespace App\Support\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use DateTimeInterface;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer;
use RuntimeException;

class ExportRenderer
{
    /**
     * @param  iterable<object|array<string, mixed>>  $rows
     * @param  list<ExportColumn>  $columns
     */
    public function writeXlsx(iterable $rows, array $columns, string $path): void
    {
        $writer = new Writer;
        $writer->openToFile($path);

        try {
            $writer->addRow($this->row(array_map(
                fn (ExportColumn $column) => $column->label,
                $columns
            )));

            foreach ($rows as $row) {
                $writer->addRow($this->row(array_map(
                    fn (ExportColumn $column) => $this->cellValue(data_get($row, $column->key)),
                    $columns
                )));
            }
        } finally {
            $writer->close();
        }
    }

    /**
     * @param  iterable<object|array<string, mixed>>  $rows
     * @param  list<ExportColumn>  $columns
     */
    public function writeCsv(iterable $rows, array $columns, string $path): void
    {
        $writer = new CsvWriter(new Options);
        $writer->openToFile($path);

        try {
            $writer->addRow($this->row(array_map(
                fn (ExportColumn $column) => $column->label,
                $columns
            )));

            foreach ($rows as $row) {
                $writer->addRow($this->row(array_map(
                    fn (ExportColumn $column) => $this->csvValue($this->cellValue(data_get($row, $column->key))),
                    $columns
                )));
            }
        } finally {
            $writer->close();
        }
    }

    /**
     * @param  iterable<object|array<string, mixed>>  $rows
     * @param  list<ExportColumn>  $columns
     */
    public function writePdf(iterable $rows, array $columns, string $path): void
    {
        $headers = implode('', array_map(
            fn (ExportColumn $column) => '<th>'.$this->escape($column->label).'</th>',
            $columns
        ));
        $body = '';

        foreach ($rows as $row) {
            $cells = implode('', array_map(
                fn (ExportColumn $column) => '<td>'.$this->escape($this->cellValue(data_get($row, $column->key))).'</td>',
                $columns
            ));
            $body .= '<tr>'.$cells.'</tr>';
        }

        $html = '<!doctype html><html lang="vi"><head><meta charset="UTF-8"><style>'
            .'@page { margin: 18px; } body { font-family: "'.htmlspecialchars((string) config('studentmanager.export.pdf_font'), ENT_QUOTES, 'UTF-8').'", sans-serif; font-size: 9px; }'
            .'table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #777; padding: 4px; }'
            .'th { background: #eee; }'
            .'</style></head><body><table><thead><tr>'.$headers.'</tr></thead><tbody>'.$body.'</tbody></table></body></html>';

        $pdf = Pdf::loadHTML($html, 'UTF-8')->setPaper('a4', 'landscape')->setOptions([
            'defaultFont' => config('studentmanager.export.pdf_font'),
            'isRemoteEnabled' => false,
            'isPhpEnabled' => false,
            'isHtml5ParserEnabled' => true,
        ]);

        if (file_put_contents($path, $pdf->output()) === false) {
            throw new RuntimeException('Không thể ghi nội dung PDF vào tệp tạm.');
        }
    }

    private function cellValue(mixed $value): string|int|float|bool|null
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        if (is_string($value) || is_int($value) || is_float($value) || is_bool($value) || $value === null) {
            return $value;
        }

        return (string) $value;
    }

    /** @param  list<string|int|float|bool|null>  $values */
    private function row(array $values): Row
    {
        return new Row(array_map(
            fn (string|int|float|bool|null $value) => is_string($value)
                ? new StringCell($value, null)
                : Cell::fromValue($value),
            $values
        ));
    }

    private function escape(string|int|float|bool|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function csvValue(string|int|float|bool|null $value): string|int|float|bool|null
    {
        if (is_string($value) && preg_match('/^[\x00-\x20]*[=+\-@]/u', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
