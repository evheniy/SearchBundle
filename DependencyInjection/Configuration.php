<?php

namespace Evheniy\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('search');

        $rootNode
            ->children()
                ->scalarNode('index_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('index_type')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('color_tag_open')->isRequired()->cannotBeEmpty()->defaultValue('<b class="yellow">')->end()
                ->scalarNode('color_tag_close')->isRequired()->cannotBeEmpty()->defaultValue('</b>')->end()
                ->arrayNode('search')
                    ->children()
                        ->arrayNode('fields')->isRequired()->cannotBeEmpty()->prototype('scalar')->end()->end()
                        ->arrayNode('parameters')
                            ->children()
                                ->scalarNode('fuzziness')->isRequired()->cannotBeEmpty()->defaultValue(0.6)->end()
                                ->scalarNode('operator')->isRequired()->cannotBeEmpty()->defaultValue('or')->end()
                                ->scalarNode('type')->isRequired()->cannotBeEmpty()->defaultValue('best_fields')->end()
                                ->scalarNode('tie_breaker')->isRequired()->cannotBeEmpty()->defaultValue(0.3)->end()
                                ->scalarNode('minimum_should_match')->isRequired()->cannotBeEmpty()->defaultValue('30%')->end()
                            ->end()
                        ->end()
                        ->arrayNode('filter')
                            ->children()
                                ->arrayNode('fields')->isRequired()->cannotBeEmpty()->prototype('scalar')->end()->end()
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