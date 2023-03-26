<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\TgReferralLinkRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $link
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class TgReferralLink extends Model
{
    use EntityPhpDoc;
    use SoftDeletes;
    use TgReferralLinkRelations;

    protected $fillable = [
        'user_id',
        'link',
        'name',
    ];

    public function getMorphClass(): string
    {
        return 'TgReferralLink';
    }
}
