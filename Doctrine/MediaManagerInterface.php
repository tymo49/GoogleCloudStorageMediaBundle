<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Doctrine;

use AppVerk\Components\Doctrine\ManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaManagerInterface extends ManagerInterface
{
    public function createMedia(UploadedFile $uploadedFile, string $fileName, int $size);
}
