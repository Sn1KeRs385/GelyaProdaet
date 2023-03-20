<?php

return [
    'image' => [
        'queue' => 'default',
        'disk' => 's3',
        'jobs' => [
            \Sn1KeRs385\FileUploader\App\Jobs\FileConverter::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromFile::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromImage::class,
        ],
    ],
];
