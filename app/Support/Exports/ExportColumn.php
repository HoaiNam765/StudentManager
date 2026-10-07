<?php

namespace App\Support\Exports;

use InvalidArgumentException;

final readonly class ExportColumn
{
    public function __construct(
        public string $key,
        public string $label,
        public bool $sensitive = false,
    ) {
        if (trim($this->key) === '' || trim($this->label) === '') {
            throw new InvalidArgumentException('Cột xuất phải có mã và tiêu đề.');
        }
    }

    /** @return array{key: string, label: string, sensitive: bool} */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'sensitive' => $this->sensitive,
        ];
    }

    /** @param  array{key: string, label: string, sensitive?: bool}  $data */
    public static function fromArray(array $data): self
    {
        return new self($data['key'], $data['label'], $data['sensitive'] ?? false);
    }
}
