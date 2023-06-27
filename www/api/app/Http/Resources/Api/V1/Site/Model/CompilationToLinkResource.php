<?php

namespace App\Http\Resources\Api\V1\Site\Model;

use App\Models\Compilation;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Compilation
 */
class CompilationToLinkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->name,
            'url' => $this->sitePages[0]->url ?? null,
        ];
    }
}
