<?php

namespace App\Dto;


use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\DataTransferObject\DataTransferObject;

class PaginationResourceDto extends DataTransferObject
{
    public Paginator $paginator;
    public string|JsonResource $itemResourceClass;
}
