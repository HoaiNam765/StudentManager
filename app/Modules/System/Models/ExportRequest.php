<?php

namespace App\Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class ExportRequest extends Model
{
    protected $table = 'export_requests';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'export_scopes' => 'array',
            'view_scopes' => 'array',
            'row_count' => 'integer',
        ];
    }
}
