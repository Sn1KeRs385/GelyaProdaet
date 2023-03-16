<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\ProductItemRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $product_id
 * @property int $size_id
 * @property int|null $color_id
 * @property int $price
 * @property bool $is_sold
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductItem extends Model
{
    use EntityPhpDoc;
    use ProductItemRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'price',
        'is_sold',
    ];
}
