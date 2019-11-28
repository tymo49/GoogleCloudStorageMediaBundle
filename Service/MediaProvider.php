<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use AppVerk\GoogleCloudStorageMediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MediaProvider
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getUrl(Media $media): string
    {
        return $media->getFileName();
    }

    public function getPublicUrl(Media $media): string
    {
        return $this->request->getSchemeAndHttpHost() . $this->getUrl($media);
    }
}
