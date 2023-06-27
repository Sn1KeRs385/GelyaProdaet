<?php

namespace App\Http\Resources\Api\V1\Site\Model;

use App\Models\ListOption;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ListOption
 */
class ListOptionResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'group_slug' => $this->group_slug,
            'group_slug_human' => $this->groupSlugHuman,
            'title' => $this->title,
        ];

        if ($this->relationLoaded('files')) {
            $data['logo'] = FileResource::collection($this->logo);
        }

        return $data;
    }
}
