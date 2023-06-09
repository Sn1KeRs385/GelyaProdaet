<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $import_task_id
 * @property int $ozon_product_id
 * @property string|null $offer_id
 * @property int|null $external_product_id
 * @property string|null $status
 * @property array $errors
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OzonImportTaskResult extends Model
{
    use EntityPhpDoc;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'import_task_id',
        'ozon_product_id',
        'offer_id',
        'external_product_id',
        'status',
        'errors',
    ];

    protected $casts = [
        'errors' => 'array',
    ];

    public function getMorphClass(): string
    {
        return 'OzonImportTaskResult';
    }
}
