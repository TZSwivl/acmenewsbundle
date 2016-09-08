<?php

namespace Acme\NewsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('acme_news');

        $rootNode
            ->children()
                ->arrayNode('news_per_page')
                    ->useAttributeAsKey('name')
                    ->children()
                        ->integerNode('html')
                            ->isRequired()
                            ->min(1)
                            ->max(100)
                            ->defaultValue(10)
                        ->end()
                        ->integerNode('xml')
                            ->isRequired()
                            ->min(1)
                            ->max(100)
                            ->defaultValue(10)
                        ->end()
                        ->integerNode('in_block')
                            ->isRequired()
                            ->min(1)
                            ->max(20)
                            ->defaultValue(5)
                        ->end()
                    ->end()
                ->end() // news_per_page
                ->arrayNode('memcached')
                    ->canBeDisabled()
                    ->useAttributeAsKey('name')
                    ->children()
                        ->scalarNode('service_name')->defaultValue('memcached')->end()
                    ->end()
                ->end() // memcached
            ->end() // children of root
        ;

        return $treeBuilder;
    }
}