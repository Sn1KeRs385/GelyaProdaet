<?php

namespace App\Models;

use App\Models\Traits\Attributes\ProductItemAttributes;
use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\ProductItemRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $product_id
 * @property int $size_id
 * @property int|null $color_id
 * @property int $price_buy
 * @property int $price
 * @property int $count
 * @property bool $is_sold
 * @property bool $is_for_sale
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductItem extends Model
{
    use EntityPhpDoc;
    use ProductItemRelations;
    use ProductItemAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'price_buy',
        'price',
        'count',
        'is_sold',
        'is_for_sale',
    ];

    public function getMorphClass(): string
    {
        return 'ProductItem';
    }
}
