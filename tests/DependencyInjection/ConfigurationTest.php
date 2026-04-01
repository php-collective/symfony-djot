<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Tests\DependencyInjection;

use PhpCollective\SymfonyDjot\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $config = $this->processConfiguration([]);

        $this->assertArrayHasKey('converters', $config);
        $this->assertArrayHasKey('default', $config['converters']);
        $this->assertTrue($config['converters']['default']['safe_mode']);
        $this->assertSame([], $config['converters']['default']['extensions']);
        $this->assertFalse($config['cache']['enabled']);
        $this->assertSame('cache.app', $config['cache']['pool']);
    }

    public function testConverterWithSafeMode(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'user_content' => [
                    'safe_mode' => true,
                ],
            ],
        ]);

        $this->assertTrue($config['converters']['user_content']['safe_mode']);
    }

    public function testConverterWithExtensions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        ['type' => 'autolink'],
                        ['type' => 'smart_quotes'],
                    ],
                ],
            ],
        ]);

        $this->assertCount(2, $config['converters']['default']['extensions']);
        $this->assertSame('autolink', $config['converters']['default']['extensions'][0]['type']);
        $this->assertSame('smart_quotes', $config['converters']['default']['extensions'][1]['type']);
    }

    public function testExtensionShorthandSyntax(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        'autolink',
                        'smart_quotes',
                    ],
                ],
            ],
        ]);

        $this->assertSame('autolink', $config['converters']['default']['extensions'][0]['type']);
        $this->assertSame('smart_quotes', $config['converters']['default']['extensions'][1]['type']);
    }

    public function testAutolinkExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'autolink',
                            'allowed_schemes' => ['https', 'mailto'],
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame(['https', 'mailto'], $ext['allowed_schemes']);
    }

    public function testExternalLinksExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'external_links',
                            'internal_hosts' => ['example.com', 'localhost'],
                            'target' => '_blank',
                            'rel' => 'noopener',
                            'nofollow' => true,
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame(['example.com', 'localhost'], $ext['internal_hosts']);
        $this->assertSame('_blank', $ext['target']);
        $this->assertSame('noopener', $ext['rel']);
        $this->assertTrue($ext['nofollow']);
    }

    public function testHeadingPermalinksExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'heading_permalinks',
                            'symbol' => '#',
                            'position' => 'after',
                            'class' => 'anchor',
                            'aria_label' => 'Permalink',
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame('#', $ext['symbol']);
        $this->assertSame('after', $ext['position']);
        $this->assertSame('anchor', $ext['class']);
        $this->assertSame('Permalink', $ext['aria_label']);
    }

    public function testMentionsExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'mentions',
                            'user_url_template' => '/users/{username}',
                            'user_class' => 'mention',
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame('/users/{username}', $ext['user_url_template']);
        $this->assertSame('mention', $ext['user_class']);
    }

    public function testSmartQuotesExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'smart_quotes',
                            'locale' => 'de',
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame('de', $ext['locale']);
    }

    public function testTableOfContentsExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'table_of_contents',
                            'min_level' => 2,
                            'max_level' => 4,
                            'toc_class' => 'table-of-contents',
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame(2, $ext['min_level']);
        $this->assertSame(4, $ext['max_level']);
        $this->assertSame('table-of-contents', $ext['toc_class']);
    }

    public function testWikilinksExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'wikilinks',
                            'url_template' => '/wiki/{page}',
                            'link_class' => 'wiki-link',
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame('/wiki/{page}', $ext['url_template']);
        $this->assertSame('wiki-link', $ext['link_class']);
    }

    public function testDefaultAttributesExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'default_attributes',
                            'defaults' => [
                                'image' => ['loading' => 'lazy'],
                                'table' => ['class' => 'table'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertIsArray($ext['defaults']);
        $defaults = $ext['defaults'];
        $this->assertSame(['loading' => 'lazy'], $defaults['image']);
        $this->assertSame(['class' => 'table'], $defaults['table']);
    }

    public function testFrontmatterExtensionOptions(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'frontmatter',
                            'default_format' => 'toml',
                            'render_as_comment' => true,
                        ],
                    ],
                ],
            ],
        ]);

        $ext = $config['converters']['default']['extensions'][0];
        $this->assertSame('toml', $ext['default_format']);
        $this->assertTrue($ext['render_as_comment']);
    }

    public function testCacheConfiguration(): void
    {
        $config = $this->processConfiguration([
            'cache' => [
                'enabled' => true,
                'pool' => 'cache.custom',
            ],
        ]);

        $this->assertTrue($config['cache']['enabled']);
        $this->assertSame('cache.custom', $config['cache']['pool']);
    }

    public function testMultipleConverters(): void
    {
        $config = $this->processConfiguration([
            'converters' => [
                'default' => [
                    'safe_mode' => false,
                ],
                'user_content' => [
                    'safe_mode' => true,
                ],
                'documentation' => [
                    'extensions' => [
                        ['type' => 'table_of_contents'],
                    ],
                ],
            ],
        ]);

        $this->assertCount(3, $config['converters']);
        $this->assertFalse($config['converters']['default']['safe_mode']);
        $this->assertTrue($config['converters']['user_content']['safe_mode']);
        $this->assertCount(1, $config['converters']['documentation']['extensions']);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array{converters: array<string, array{safe_mode: bool, extensions: list<array<string, mixed>>}>, cache: array{enabled: bool, pool: string}}
     */
    private function processConfiguration(array $config): array
    {
        $processor = new Processor();

        /** @var array{converters: array<string, array{safe_mode: bool, extensions: list<array<string, mixed>>}>, cache: array{enabled: bool, pool: string}} */
        return $processor->processConfiguration(new Configuration(), [$config]);
    }
}
