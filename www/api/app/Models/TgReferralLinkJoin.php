<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\TgReferralLinkJoinRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property int|null $link_id
 * @property string $link
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TgReferralLinkJoin extends Model
{
    use EntityPhpDoc;
    use SoftDeletes;
    use TgReferralLinkJoinRelations;

    protected $fillable = [
        'link_id',
        'link',
        'user_id',
    ];

    public function getMorphClass(): string
    {
        return 'TgReferralLinkUser';
    }
}
