<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;

class LocalObjectUrlRetriever implements UrlRetrieverInterface
{
    public function getUrl(string $filename): string
    {
        return $filename;
    }
}
