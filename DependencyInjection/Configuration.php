<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('google_cloud_storage_media');

        $rootNode
            ->children()
                ->arrayNode('entities')
                    ->children()
                        ->scalarNode('media_class')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->arrayNode('gcs')
            ->children()
            ->scalarNode('project_id')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->children()
            ->scalarNode('bucket_id')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->children()
            ->scalarNode('key_file_path')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
                ->scalarNode('max_file_size')
                    ->defaultValue(5242880)
                ->end()
                ->scalarNode('media_root_dir')
                    ->defaultValue('%kernel.project_dir%/web/uploads/media')
                ->end()
                ->scalarNode('media_web_path')
                    ->defaultValue('/uploads/media')
                ->end()
                ->arrayNode('allowed_mime_types')
                    ->scalarPrototype()
                    ->end()
                ->end()
                ->arrayNode('groups')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                       ->children()
                            ->scalarNode('max_file_size')->defaultValue(5242880)->end()
                            ->arrayNode('allowed_mime_types')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('dir')->defaultValue('')->end()
                            ->arrayNode('sizes')
                                ->children()
                                    ->scalarNode('min_width')->defaultValue(0)->end()
                                    ->scalarNode('min_height')->defaultValue(0)->end()
                                    ->scalarNode('max_width')->defaultValue(0)->end()
                                    ->scalarNode('max_height')->defaultValue(0)->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
