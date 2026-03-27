<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfony_djot');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('converters')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('safe_mode')
                                ->defaultFalse()
                                ->info('Enable safe mode for XSS protection when processing untrusted input')
                            ->end()
                        ->end()
                    ->end()
                    ->defaultValue([
                        'default' => [
                            'safe_mode' => false,
                        ],
                    ])
                ->end()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Enable caching of rendered output')
                        ->end()
                        ->scalarNode('pool')
                            ->defaultValue('cache.app')
                            ->info('Cache pool service to use')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
