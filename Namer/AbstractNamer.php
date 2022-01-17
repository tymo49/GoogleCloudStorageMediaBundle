<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer;

use AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy\AbstractNamingStrategy;

abstract class AbstractNamer
{
    protected AbstractNamingStrategy $strategy;

    public function __construct(AbstractNamingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function generate(string $filename, string $extension): string
    {
        return $this->strategy->generate($this->sanitize($filename), $extension);
    }

    protected function sanitize(string $filename): string
    {
        return preg_replace(['/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'], ['_', '.', ''], $filename);
    }
}
