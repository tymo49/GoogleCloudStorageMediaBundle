<?php

namespace AppVerk\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('media.entities.media_class', $config['entities']['media_class']);
        $container->setParameter('media.max_file_size', $config['max_file_size']);
        $container->setParameter('media.media_web_path', $config['media_web_path']);
        $container->setParameter('media.media_root_dir', $config['media_root_dir']);
        $container->setParameter('media.allowed_mime_types', $config['allowed_mime_types']);
        $container->setParameter('media.groups', $config['groups']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
