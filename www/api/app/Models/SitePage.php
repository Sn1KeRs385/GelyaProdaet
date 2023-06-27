<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\SitePageRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int|null $parent_id
 * @property string $url
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SitePage extends Model
{
    use EntityPhpDoc;
    use SitePageRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'url',
        'owner_type',
        'owner_id',
        'is_enabled',
    ];

    public function getMorphClass(): string
    {
        return 'SitePage';
    }
}
