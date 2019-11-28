<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Doctrine;

use AppVerk\Components\Doctrine\AbstractManager;
use AppVerk\GoogleCloudStorageMediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager extends AbstractManager implements MediaManagerInterface
{
    /**
     * @param UploadedFile $uploadedFile
     * @param string       $fileName
     * @param int          $size
     *
     * @return Media
     */
    public function createMedia(UploadedFile $uploadedFile, string $fileName, int $size): Media
    {
        /** @var Media $media */
        $media = new $this->className();
        $media->setName($uploadedFile->getClientOriginalName());
        $media->setFileName($fileName);
        $media->setSize($size);
        $media->setMimeType($uploadedFile->getClientMimeType());

        $this->persistAndFlash($media);

        return $media;
    }
}
