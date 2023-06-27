<?php

namespace App\Http\Resources\Api\V1\Site\Model;

use App\Models\Compilation;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Compilation
 */
class CompilationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
        ];
    }
}
