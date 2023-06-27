<?php

namespace App\Http\Resources\Api\V1\Site\Model;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin File
 */
class FileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
        ];
    }
}
