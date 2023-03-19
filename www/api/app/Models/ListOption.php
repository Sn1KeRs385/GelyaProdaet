<?php

namespace App\Models;

use App\Models\Traits\Attributes\ListOptionAttributes;
use App\Models\Traits\EntityPhpDoc;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $group_slug
 * @property string $title
 * @property int $weight
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ListOption extends Model
{
    use EntityPhpDoc;
    use ListOptionAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_slug',
        'title',
        'weight',
    ];

    public function getMorphClass(): string
    {
        return 'ListOption';
    }
}
