<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LengthAwarePaginator
 */
class BaseIndexResources extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'items' => $this->items(),
        ];
    }
}
