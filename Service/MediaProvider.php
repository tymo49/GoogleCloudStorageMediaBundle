<?php

namespace AppVerk\MediaBundle\Service;

use AppVerk\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MediaProvider
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $targetDirectory;


    public function __construct(RequestStack $requestStack, string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getUrl(Media $media): string
    {
        return $this->targetDirectory . DIRECTORY_SEPARATOR . $media->getFileName();
    }

    public function getPublicUrl(Media $media): string
    {
        return $this->request->getSchemeAndHttpHost() . $this->getUrl($media);
    }
}
