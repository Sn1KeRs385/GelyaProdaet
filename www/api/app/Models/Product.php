<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\ProductRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $title
 * @property int $type_id
 * @property int|null $brand_id
 * @property int|null $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Product extends Model
{
    use EntityPhpDoc;
    use ProductRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'type_id',
        'brand_id',
        'country_id',
    ];

    public function getMorphClass(): string
    {
        return 'Product';
    }
}
