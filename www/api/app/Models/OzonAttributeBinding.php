<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\OzonAttributeBindingRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property int $attribute_id
 * @property int $list_option_id
 * @property int $dictionary_value_id
 * @property int $dictionary_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class OzonAttributeBinding extends Model
{
    use EntityPhpDoc;
    use OzonAttributeBindingRelations;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'list_option_id',
        'dictionary_value_id',
        'dictionary_value',
    ];

    public function getMorphClass(): string
    {
        return 'OzonAttributeBinding';
    }
}
