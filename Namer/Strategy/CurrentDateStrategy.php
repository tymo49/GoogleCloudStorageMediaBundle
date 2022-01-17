<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy;

class CurrentDateStrategy extends AbstractNamingStrategy
{
    private string $format;

    public function __construct(string $format = 'Y/m/d/')
    {
        $this->format = $format;
    }

    public function generate(string $filename, string $extension, string $prefix = '', string $suffix = ''): string
    {
        return date($this->format) . $prefix . $filename . $suffix . '.' . $extension;
    }
}
