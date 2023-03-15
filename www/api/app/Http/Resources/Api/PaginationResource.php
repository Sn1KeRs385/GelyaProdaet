<?php

namespace App\Http\Resources\Api;

use App\Dto\PaginationResourceDto;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaginationResourceDto
 */
class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'current_page' => $this->paginator->currentPage(),
            'per_page' => $this->paginator->perPage(),
            'from' => $this->paginator->firstItem(),
            'to' => $this->paginator->lastItem(),
            'items' => $this->itemResourceClass::collection($this->paginator->items()),
        ];
    }
}
