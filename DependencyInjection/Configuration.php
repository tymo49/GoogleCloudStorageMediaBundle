<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\DependencyInjection;

use AppVerk\GoogleCloudStorageMediaBundle\Namer\NamerInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('google_cloud_storage_media');

        $rootNode = $treeBuilder->getRootNode();

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
                ->scalarNode('namer')
                    ->defaultValue(NamerInterface::class)
                ->end()
                ->scalarNode('filesystem')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('filesystem_url_retriever')
                    ->cannotBeEmpty()
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
                ->scalarNode('date_strategy_format')
                    ->defaultValue('Y/m/d/')
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
