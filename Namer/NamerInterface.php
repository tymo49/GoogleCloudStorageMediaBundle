<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer;

interface NamerInterface
{
    public function generate(string $filename, string $extension): string;
}
