<?php

namespace App\Dto;


use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelData\Data;

class PaginationResourceDto extends Data
{
    public Paginator $paginator;
    public string|JsonResource $itemResourceClass;
}
