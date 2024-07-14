<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\PathPrefixer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Storage
{
    private string $bucketId;
    private StorageClient $client;

    /**
     * Storage constructor.
     *
     * @param string $projectId
     * @param string $bucketId
     * @param string $keyFilePath
     */
    public function __construct(string $projectId, string $bucketId, string $keyFilePath)
    {
        $this->bucketId = $bucketId;
        $this->client = new StorageClient(
            [
                'projectId'   => $projectId,
                'keyFilePath' => $keyFilePath,
            ]
        );
    }

    /**
     * @return Bucket
     */
    public function bucket(): Bucket
    {
        return $this->client->bucket($this->bucketId);
    }

    public function getBucketId(): string
    {
        return  $this->bucketId;
    }
}
