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
                            ->arrayNode('extensions')
                                ->info('Extensions to enable for this converter')
                                ->arrayPrototype()
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(fn ($v) => ['type' => $v])
                                    ->end()
                                    ->children()
                                        ->scalarNode('type')
                                            ->isRequired()
                                            ->info('Extension type: autolink, default_attributes, external_links, frontmatter, heading_permalinks, mentions, semantic_span, smart_quotes, table_of_contents, wikilinks')
                                        ->end()
                                        // Autolink options
                                        ->arrayNode('allowed_schemes')
                                            ->scalarPrototype()->end()
                                            ->info('Autolink: URL schemes to auto-link (default: https, http, mailto)')
                                        ->end()
                                        // DefaultAttributes options
                                        ->arrayNode('defaults')
                                            ->useAttributeAsKey('node_type')
                                            ->variablePrototype()->end()
                                            ->info('DefaultAttributes: Map of node type to default attributes')
                                        ->end()
                                        // Frontmatter options
                                        ->scalarNode('default_format')
                                            ->info('Frontmatter: Default format (yaml, toml, json)')
                                        ->end()
                                        ->booleanNode('render_as_comment')
                                            ->info('Frontmatter: Render frontmatter as HTML comment')
                                        ->end()
                                        // External links options
                                        ->arrayNode('internal_hosts')
                                            ->scalarPrototype()->end()
                                            ->info('ExternalLinks: Hosts to treat as internal')
                                        ->end()
                                        ->scalarNode('target')
                                            ->info('ExternalLinks: Target attribute (default: _blank)')
                                        ->end()
                                        ->scalarNode('rel')
                                            ->info('ExternalLinks: Rel attribute (default: noopener noreferrer)')
                                        ->end()
                                        ->booleanNode('nofollow')
                                            ->info('ExternalLinks: Add nofollow to rel')
                                        ->end()
                                        // Heading permalinks options
                                        ->scalarNode('symbol')
                                            ->info('HeadingPermalinks: Link symbol (default: #)')
                                        ->end()
                                        ->scalarNode('position')
                                            ->info('HeadingPermalinks: Position - before or after (default: after)')
                                        ->end()
                                        ->scalarNode('class')
                                            ->info('HeadingPermalinks: CSS class for the link')
                                        ->end()
                                        ->scalarNode('aria_label')
                                            ->info('HeadingPermalinks: Aria label for accessibility')
                                        ->end()
                                        // Mentions options
                                        ->scalarNode('user_url_template')
                                            ->info('Mentions: URL template for @mentions (use {username} placeholder)')
                                        ->end()
                                        ->scalarNode('user_class')
                                            ->info('Mentions: CSS class for mention links')
                                        ->end()
                                        // Smart quotes options
                                        ->scalarNode('locale')
                                            ->info('SmartQuotes: Locale for quote styles (default: en)')
                                        ->end()
                                        // Table of contents options
                                        ->integerNode('min_level')
                                            ->info('TableOfContents: Minimum heading level (default: 1)')
                                        ->end()
                                        ->integerNode('max_level')
                                            ->info('TableOfContents: Maximum heading level (default: 6)')
                                        ->end()
                                        ->scalarNode('toc_class')
                                            ->info('TableOfContents: CSS class for the TOC container')
                                        ->end()
                                        // Wikilinks options
                                        ->scalarNode('url_template')
                                            ->info('Wikilinks: URL template (use {page} placeholder)')
                                        ->end()
                                        ->scalarNode('link_class')
                                            ->info('Wikilinks: CSS class for wiki links')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->defaultValue([
                        'default' => [
                            'safe_mode' => false,
                            'extensions' => [],
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
