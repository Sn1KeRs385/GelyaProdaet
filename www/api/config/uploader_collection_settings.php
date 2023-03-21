<?php

return [
    'image' => [
        'queue' => 'default',
        'disk' => 's3',
        'options' => [
            'optimize_if_size_more_mb' => 4.9,
        ],
        'jobs' => [
            \Sn1KeRs385\FileUploader\App\Jobs\ImageToWebpAndOptimizationConverter::class,
            \Sn1KeRs385\FileUploader\App\Jobs\FileConverter::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromFile::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromImage::class,
        ],
    ],
];
