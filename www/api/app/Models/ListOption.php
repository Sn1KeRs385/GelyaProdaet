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
 * @property boolean $is_hidden_from_user_filters
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
        'is_hidden_from_user_filters'
    ];

    public function getMorphClass(): string
    {
        return 'ListOption';
    }
}
