<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\OzonProductRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $product_id
 * @property int $color_id
 * @property int|null $size_id
 * @property int|null $size_year_id
 * @property int|null $count
 * @property int|null $external_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OzonProduct extends Model
{
    use EntityPhpDoc;
    use OzonProductRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'size_year_id',
        'count',
        'external_id',
    ];

    public function getMorphClass(): string
    {
        return 'OzonProduct';
    }

    public function getProductItemsQuery(): Builder
    {
        return ProductItem::query()
            ->where('product_id', $this->product_id)
            ->where('size_id', $this->size_id)
            ->where('size_year_id', $this->size_year_id)
            ->where('color_id', $this->color_id)
            ->where('count', $this->count);
    }
}
