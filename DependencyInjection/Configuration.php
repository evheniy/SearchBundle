<?php

namespace Evheniy\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Evheniy\SearchBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('search');
        $rootNode
            ->children()
                ->scalarNode('index_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('index_type')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('color_tag_open')->defaultValue('<b class="yellow">')->end()
                ->scalarNode('color_tag_close')->defaultValue('</b>')->end()
                ->arrayNode('search')
                    ->children()
                        ->arrayNode('fields')->isRequired()->cannotBeEmpty()->prototype('scalar')->end()->end()
                        ->arrayNode('parameters')
                            ->children()
                                ->scalarNode('fuzziness')->defaultValue(0.6)->end()
                                ->scalarNode('operator')->defaultValue('or')->end()
                                ->scalarNode('type')->defaultValue('best_fields')->end()
                                ->scalarNode('tie_breaker')->defaultValue(0.3)->end()
                                ->scalarNode('minimum_should_match')->defaultValue('30%')->end()
                                ->arrayNode('priorities')->prototype('scalar')->end()->end()
                            ->end()
                        ->end()
                        ->arrayNode('filter')
                            ->children()
                                ->arrayNode('fields')->prototype('scalar')->end()->end()
                                ->scalarNode('count')->defaultValue(10)->end()
                                ->booleanNode('analyze')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}