<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use Illuminate\Support\Facades\Storage;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;
use Sn1KeRs385\FileUploader\App\Models\File as BaseFile;

/**
 * @property string|null $permanentUrl
 */
class File extends BaseFile
{
    use EntityPhpDoc;


    public function getPermanentUrlAttribute(): ?string
    {
        if ($this->status !== FileStatus::FINISHED->value) {
            return null;
        }
        return Storage::disk($this->disk)
            ->url($this->fullPath);
    }
}
