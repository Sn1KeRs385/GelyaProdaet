<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\CompilationListOptionRelations;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read int $compilation_id
 * @property-read int $list_option_id
 */
class CompilationListOption extends Pivot
{
    use EntityPhpDoc;
    use CompilationListOptionRelations;
    
    public $timestamps = false;

    protected $fillable = [
        'compilation_id',
        'list_option_id',
    ];

    public function getMorphClass(): string
    {
        return 'CompilationListOption';
    }
}
