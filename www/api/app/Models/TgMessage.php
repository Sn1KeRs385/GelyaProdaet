<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\ProductRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $chat_id
 * @property int $message_id
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property array $file_ids
 * @property bool $is_forward_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TgMessage extends Model
{
    use EntityPhpDoc;

    protected $fillable = [
        'chat_id',
        'message_id',
        'owner_type',
        'owner_id',
        'file_ids',
        'is_forward_error',
    ];

    protected $casts = [
        'file_ids' => 'array',
    ];

    public function getMorphClass(): string
    {
        return 'TgMessage';
    }
}
