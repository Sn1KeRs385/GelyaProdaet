<?php

namespace App\Models;

use App\Models\Traits\Attributes\ProductAttributes;
use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\ProductRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $title
 * @property string|null $description
 * @property int $type_id
 * @property int $gender_id
 * @property int|null $brand_id
 * @property int|null $country_id
 * @property bool $send_to_telegram
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Product extends Model
{
//    use EntityPhpDoc;
    use ProductRelations;
    use ProductAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'type_id',
        'gender_id',
        'brand_id',
        'country_id',
        'send_to_telegram'
    ];

    public function getMorphClass(): string
    {
        return 'Product';
    }
}
