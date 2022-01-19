<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy;

interface NamingStrategyInterface
{
    public function generate(string $filename, string $extension, string $prefix = '', string $suffix = ''): string;
}
