<?php

namespace App\Http\Resources\Model;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin File
 */
class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'status' => $this->status,
            'collection' => $this->collection,
            'filename' => $this->original_filename,
            'type' => $this->type,
            'url' => $this->url,
            'files' => null,
        ];

        if (count($this->files) > 0) {
            $array['files'] = self::collection($this->files);
        }

        return $array;
    }
}
