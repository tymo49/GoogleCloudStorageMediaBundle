<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer;

class DefaultNamer extends AbstractNamer
{
    public function generate(string $filename, string $extension): string
    {
        return $this->strategy->generate($this->sanitize($filename), $extension, '', uniqid('_'));
    }
}
