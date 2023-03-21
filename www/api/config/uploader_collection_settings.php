<?php

return [
    'image' => [
        'queue' => 'file-converter',
        'disk' => 's3',
        'options' => [
            'optimize_if_size_more' => 5,
            'zip_to' => 5,
        ],
        'jobs' => [
            \Sn1KeRs385\FileUploader\App\Jobs\StartConverting::class,
            \Sn1KeRs385\FileUploader\App\Jobs\FileConverter::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ImageOptimize::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ImageZipToSize::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromFile::class,
            \Sn1KeRs385\FileUploader\App\Jobs\ExtractMetaDataFromImage::class,
            \Sn1KeRs385\FileUploader\App\Jobs\EndConverting::class,
            \App\Jobs\SendProductToTelegramAfterImagesUploading::class,
        ],
    ],
];
