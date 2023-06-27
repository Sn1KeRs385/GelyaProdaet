<?php

namespace App\Http\Resources\Api\V1\Site\Header;

use App\Dto\Services\V1\SiteServiceDto\HeaderDto;
use App\Http\Resources\Api\V1\Site\Model\CompilationToLinkResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HeaderDto
 */
class SiteHeaderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'links' => CompilationToLinkResource::collection($this->compilationLinks),
        ];
    }
}
