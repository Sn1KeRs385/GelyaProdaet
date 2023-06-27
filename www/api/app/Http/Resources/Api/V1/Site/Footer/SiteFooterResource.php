<?php

namespace App\Http\Resources\Api\V1\Site\Footer;

use App\Dto\Services\V1\SiteServiceDto\HeaderDto;
use App\Http\Resources\Api\V1\Site\Model\CompilationToLinkResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HeaderDto
 */
class SiteFooterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'links' => CompilationToLinkResource::collection($this->compilationLinks),
        ];
    }
}
