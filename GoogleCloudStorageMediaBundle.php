<?php

namespace AppVerk\GoogleCloudStorageMediaBundle;

use AppVerk\GoogleCloudStorageMediaBundle\DependencyInjection\Compiler\AddStorageServiceMappingPass;
use NovolComponents\DependencyInjection\Compiler\AccessControlHandlerPass;
use NovolComponents\DependencyInjection\Compiler\AddDoctrineMappingPass;
use NovolComponents\DependencyInjection\Compiler\ConfigureMessageBusesPass;
use NovolComponents\DependencyInjection\Compiler\RegisterTestHelpersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GoogleCloudStorageMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddStorageServiceMappingPass());
    }
}
