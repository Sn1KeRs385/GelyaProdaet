<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property string $chat_id
 * @property int $message_id
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property array $file_ids
 * @property array $extra_message_ids
 * @property bool $is_not_found_error
 * @property bool $is_messages_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class TgMessage extends Model
{
    use EntityPhpDoc;
    use SoftDeletes;

    protected $fillable = [
        'chat_id',
        'message_id',
        'owner_type',
        'owner_id',
        'file_ids',
        'is_not_found_error',
        'is_messages_deleted',
        'extra_message_ids',
    ];

    protected $casts = [
        'file_ids' => 'array',
        'extra_message_ids' => 'array',
    ];

    public function getMorphClass(): string
    {
        return 'TgMessage';
    }
}
