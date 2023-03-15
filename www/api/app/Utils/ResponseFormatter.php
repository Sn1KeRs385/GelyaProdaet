<?php

namespace App\Utils;


use App\Dto\PaginationResourceDto;
use App\Http\Resources\Api\PaginationResource;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseFormatter
{
    public function formatPaginator(Paginator $paginator, string|JsonResource $itemResourceClass): PaginationResource
    {
        $paginatorDto = new PaginationResourceDto(
            paginator: $paginator,
            itemResourceClass: $itemResourceClass,
        );

        return PaginationResource::make($paginatorDto);
    }
}
