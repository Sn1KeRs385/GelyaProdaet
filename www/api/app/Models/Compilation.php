<?php

namespace App\Models;

use App\Enums\CompilationType;
use App\Models\Traits\Attributes\CompilationAttributes;
use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\CompilationRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int|null $user_id
 * @property CompilationType $type
 * @property string $name
 * @property bool $is_show_on_main_page
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Collection<int, Product>|Product[] $products Заполняется в коде
 */
class Compilation extends Model
{
    use EntityPhpDoc;
    use CompilationAttributes;
    use CompilationRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'is_show_on_main_page',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => CompilationType::class,
    ];

    public function getMorphClass(): string
    {
        return 'Compilation';
    }
}
