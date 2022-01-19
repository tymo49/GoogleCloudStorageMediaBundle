<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Event;

use AppVerk\GoogleCloudStorageMediaBundle\Model\UploadFile;

class FileWasAdded
{
    private UploadFile $file;

    public function __construct(UploadFile $file) {
        $this->file = $file;
    }

    public function getFile(): UploadFile
    {
        return $this->file;
    }
}
