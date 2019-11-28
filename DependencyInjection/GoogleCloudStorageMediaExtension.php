<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class GoogleCloudStorageMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('google_cloud_storage_media.entities.media_class', $config['entities']['media_class']);
        $container->setParameter('google_cloud_storage_media.max_file_size', $config['max_file_size']);
        $container->setParameter('google_cloud_storage_media.media_web_path', $config['media_web_path']);
        $container->setParameter('google_cloud_storage_media.media_root_dir', $config['media_root_dir']);
        $container->setParameter('google_cloud_storage_media.gcs.project_id', $config['media_gcs_project_id']);
        $container->setParameter('google_cloud_storage_media.gcs.bucket_id', $config['media_gcs_bucket_id']);
        $container->setParameter('google_cloud_storage_media.allowed_mime_types', $config['allowed_mime_types']);
        $container->setParameter('google_cloud_storage_media.groups', $config['groups']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
