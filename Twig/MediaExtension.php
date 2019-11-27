<?php

namespace AppVerk\MediaBundle\Twig;

use AppVerk\MediaBundle\Entity\Media;
use AppVerk\MediaBundle\Service\MediaProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MediaExtension extends AbstractExtension
{
    /**
     * @var MediaProvider
     */
    private $mediaProvider;

    public function __construct(MediaProvider $mediaProvider)
    {
        $this->mediaProvider = $mediaProvider;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('media', [$this, 'mediaFilter']),
            new TwigFilter('json_media', [$this, 'jsonMediaFilter'], [
                'is_safe' => ['html']
            ]),
            new TwigFilter('array_media', [$this, 'arrayMediaFilter'], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function jsonMediaFilter($media)
    {
        $data = [];

        if ($media instanceof Media) {
            $data = [
                'name'      => $media->getName(),
                'url'       => $this->mediaProvider->getUrl($media),
                'mime_type' => $media->getMimeType(),
                'size'      => $media->getSize()
            ];
        }

        return json_encode($data);
    }

    public function arrayMediaFilter($media)
    {
        $data = [];

        if ($media instanceof Media) {
            $data = [
                'name'      => $media->getName(),
                'url'       => $this->mediaProvider->getUrl($media),
                'mime_type' => $media->getMimeType(),
                'size'      => $media->getSize()
            ];
        }

        return $data;
    }

    public function mediaFilter(Media $media)
    {
        return $this->mediaProvider->getUrl($media);
    }
}
