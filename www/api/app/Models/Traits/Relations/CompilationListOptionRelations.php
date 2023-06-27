<?php

namespace App\Models\Traits\Relations;

use App\Models\Compilation;
use App\Models\ListOption;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Compilation $compilation
 * @property ListOption $listOption
 */
trait CompilationListOptionRelations
{
    public function compilation(): BelongsTo
    {
        return $this->belongsTo(Compilation::class);
    }

    public function listOption(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }
}
