<?php

return [
    'image' => [
        'queue' => 'default',
        'disk' => 'minio-files',
        'options' => [
            'img_preview' => [
                'width' => 300,
                'height' => 300
            ],
        ],
        'jobs' => [
            \Sn1KeRs385\FileUploader\App\Jobs\FileConverter::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromFile::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromImage::class,
            \Sn1KeRs385\FileUploader\App\Jobs\PreviewImageConverter::class,
        ],
    ],
    'video' => [
        'queue' => 'default',
        'disk' => 'minio-files',
        'options' => [
            'img_preview' => [
                'width' => 300,
                'height' => 300
            ],
        ],
        'jobs' => [
            \Sn1KeRs385\FileUploader\App\Jobs\FileConverter::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromFile::class,
            \Sn1KeRs385\FileUploader\App\Jobs\PreviewVideoConverter::class,
        ],
    ],
];
