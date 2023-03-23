<?php

return [
    'directories' => [
        'temp' => 'temp/',
        'final' => 'public/'
    ],

    'queue' => 'file-converter-super',
    'start_uploader_job_on_finish' => false,

    /**
     * Время истечения временных ссылок на файлы в минутах
     */
    'temporary_url_expiration' => 60,

    'services' => [
        \Sn1KeRs385\FileUploader\App\Services\IUploaderService::class => \Sn1KeRs385\FileUploader\App\Services\UploaderService::class,
    ],
    'controllers' => [
        'UploaderController' => \Sn1KeRs385\FileUploader\App\Http\Controllers\UploaderController::class,
    ],
    'policies' => [
        \Sn1KeRs385\FileUploader\App\Models\File::class => \Sn1KeRs385\FileUploader\App\Policies\FilePolicy::class,
    ],
    'formatters' => [
        \Sn1KeRs385\FileUploader\App\Services\IFormatter::class => \Sn1KeRs385\FileUploader\App\Services\Formatter::class,
    ],
    'exceptions' => [
        \Sn1KeRs385\FileUploader\App\Enums\ExceptionCode::MAX_FILE_SIZE_EXCEEDED->value => \Sn1KeRs385\FileUploader\App\Exceptions\MaxFileSizeExceededException::class,
        \Sn1KeRs385\FileUploader\App\Enums\ExceptionCode::COLLECTION_DISK_NOT_CONFIGURED->value => \Sn1KeRs385\FileUploader\App\Exceptions\CollectionDiskNotConfiguredException::class,
    ],
];
