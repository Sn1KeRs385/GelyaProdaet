<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\FileRelations;
use Sn1KeRs385\FileUploader\App\Models\File as BaseFile;

class File extends BaseFile
{
    use EntityPhpDoc;
    use FileRelations;
}
