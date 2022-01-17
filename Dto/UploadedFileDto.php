<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Dto;

class UploadedFileDto {

    private ?string $name;

    private ?string $url;

    private ?string $mimetype;

    private ?int $size;

    public function __construct(
        ?string $name,
        ?string $url,
        ?string $mimetype,
        ?int $size
    ) {
        $this->name = $name;
        $this->url = $url;
        $this->mimetype = $mimetype;
        $this->size = $size;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

}