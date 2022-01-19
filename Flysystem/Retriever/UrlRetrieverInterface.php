<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever;

interface UrlRetrieverInterface
{
    public function getUrl(string $filename): string;
}
