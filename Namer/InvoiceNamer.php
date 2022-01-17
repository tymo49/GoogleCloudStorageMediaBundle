<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer;

class InvoiceNamer extends AbstractNamer
{
    public function generate(string $filename, string $extension): string
    {
        return $this->strategy->generate($this->sanitize($filename), $extension, 'inv_', uniqid('_'));
    }
}
