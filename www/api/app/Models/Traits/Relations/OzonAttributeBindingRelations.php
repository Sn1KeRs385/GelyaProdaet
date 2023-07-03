<?php

namespace App\Models\Traits\Relations;


use App\Models\ListOption;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ListOption $listOption
 */
trait OzonAttributeBindingRelations
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }
}
