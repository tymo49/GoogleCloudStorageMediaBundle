<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Model;

class UploadFile {

    private string $name;

    private string $fileName;

    private string $url;

    private string $mimetype;

    private int $size;

    public function __construct(
        string $name,
        string $fileName,
        string $url,
        string $mimetype,
        int $size
    ) {
        $this->name = $name;
        $this->fileName = $fileName;
        $this->url = $url;
        $this->mimetype = $mimetype;
        $this->size = $size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getSize(): int
    {
        return $this->size;
    }

}
