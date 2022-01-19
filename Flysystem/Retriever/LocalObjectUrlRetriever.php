<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever;

class LocalObjectUrlRetriever implements UrlRetrieverInterface
{
    private string $publicPath;

    public function __construct(string $publicPath = '/') {

        $this->publicPath = $publicPath;
    }

    public function getUrl(string $filename): string
    {
        return $this->publicPath . DIRECTORY_SEPARATOR . $filename;
    }
}
