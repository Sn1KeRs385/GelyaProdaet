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
 * @property int|null $size_id
 * @property int|null $size_year_id
 * @property int|null $color_id
 * @property int $price_buy
 * @property int $price
 * @property int|null $price_final
 * @property int|null $price_sell
 * @property int $count
 * @property bool $is_sold
 * @property bool $is_for_sale
 * @property bool $is_reserved
 * @property Carbon|null $sold_at
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
        'size_year_id',
        'color_id',
        'price_buy',
        'price',
        'price_final',
//        'price_sell',
        'count',
//        'is_sold',
        'is_for_sale',
//        'is_reserved',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    public function getMorphClass(): string
    {
        return 'ProductItem';
    }
}
