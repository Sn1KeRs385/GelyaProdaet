<?php

namespace App\Http\Resources\Api\V1\Site\Index;

use App\Http\Resources\Api\V1\Site\Model\CompilationResource;
use App\Http\Resources\Api\V1\Site\Model\ProductResource;
use App\Models\Compilation;

/**
 * @mixin Compilation
 */
class CompilationWithProductsResource extends CompilationResource
{
    public function toArray($request)
    {
        $array = parent::toArray($request);

        $array['products'] = ProductResource::collection($this->products);

        return $array;
    }
}
