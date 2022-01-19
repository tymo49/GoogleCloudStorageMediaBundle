<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\DependencyInjection\Compiler;

use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaValidation;
use AppVerk\GoogleCloudStorageMediaBundle\Service\v2\StorageService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AddStorageServiceMappingPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('google_cloud_storage_media.filesystem')) {
            return;
        }

        $filesystemId = (string) $container->getParameter('google_cloud_storage_media.filesystem');
        $namerId = (string) $container->getParameter('google_cloud_storage_media.namer');
        $urlRetriever = (string) $container->getParameter('google_cloud_storage_media.filesystem_url_retriever');

        $storageDefinition = $container->getDefinition(StorageService::class);
        $storageDefinition->addArgument(new Reference(MediaValidation::class));
        $storageDefinition->addArgument(new Reference($namerId));
        $storageDefinition->addArgument(new Reference($filesystemId));
        $storageDefinition->addArgument(new Reference('translator'));
        $storageDefinition->addArgument(new Reference($urlRetriever));
        $storageDefinition->addArgument(new Reference(EventDispatcherInterface::class));
    }
}
