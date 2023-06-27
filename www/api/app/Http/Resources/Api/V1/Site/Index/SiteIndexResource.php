<?php

namespace App\Http\Resources\Api\V1\Site\Index;

use App\Dto\Services\V1\SiteServiceDto\IndexPageDto;
use App\Http\Resources\Api\V1\Site\Footer\SiteFooterResource;
use App\Http\Resources\Api\V1\Site\Header\SiteHeaderResource;
use App\Http\Resources\Api\V1\Site\Model\ListOptionResource;
use App\Http\Resources\Api\V1\Site\Model\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin IndexPageDto
 */
class SiteIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product_types' => ListOptionResource::collection($this->productTypes),
            'last_products' => ProductResource::collection($this->lastProducts),
            'compilations' => CompilationWithProductsResource::collection($this->compilations),
            'header' => SiteHeaderResource::make($this->headerData),
            'footer' => SiteFooterResource::make($this->footerData),
        ];
    }
}
