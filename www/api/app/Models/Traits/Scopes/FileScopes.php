<?php

namespace App\Models\Traits\Scopes;


use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder|self selectForSite()
 */
trait FileScopes
{
    public function scopeSelectForSite($query): void
    {
        $query->select('id', 'disk', 'owner_type', 'owner_id', 'status', 'collection', 'path', 'filename');
    }
}
