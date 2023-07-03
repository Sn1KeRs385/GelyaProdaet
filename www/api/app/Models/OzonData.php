<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\OzonDataRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $product_id
 * @property int $category_id
 * @property int $dept
 * @property int $height
 * @property int $width
 * @property int $weight
 * @property array $attributes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OzonData extends Model
{
    use EntityPhpDoc;
    use OzonDataRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'category_id',
        'dept',
        'height',
        'width',
        'weight',
        'attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function getMorphClass(): string
    {
        return 'OzonData';
    }
}
