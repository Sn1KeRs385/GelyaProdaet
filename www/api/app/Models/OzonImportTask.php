<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $task_id
 * @property bool $is_completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OzonImportTask extends Model
{
    use EntityPhpDoc;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'is_completed',
    ];

    public function getMorphClass(): string
    {
        return 'OzonImportTask';
    }
}
