<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Tests\DependencyInjection;

use Djot\Extension\WikilinksExtension;
use PhpCollective\SymfonyDjot\DependencyInjection\SymfonyDjotExtension;
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SymfonyDjotExtensionTest extends TestCase
{
    public function testDefaultConverterIsRegistered(): void
    {
        $container = $this->createContainer([]);

        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default'));
        $this->assertTrue($container->hasAlias(DjotConverterInterface::class));
        $this->assertTrue($container->hasAlias('symfony_djot.converter'));
    }

    public function testConverterWithSafeMode(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'safe' => [
                    'safe_mode' => true,
                ],
            ],
        ]);

        $definition = $container->getDefinition('symfony_djot.converter.safe');
        $this->assertTrue($definition->getArgument('$safeMode'));
    }

    public function testConverterWithoutSafeMode(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'safe_mode' => false,
                ],
            ],
        ]);

        $definition = $container->getDefinition('symfony_djot.converter.default');
        $this->assertFalse($definition->getArgument('$safeMode'));
    }

    public function testCacheIsDisabledByDefault(): void
    {
        $container = $this->createContainer([]);

        $definition = $container->getDefinition('symfony_djot.converter.default');
        $this->assertNull($definition->getArgument('$cache'));
    }

    public function testCacheCanBeEnabled(): void
    {
        $container = $this->createContainer([
            'cache' => [
                'enabled' => true,
                'pool' => 'cache.app',
            ],
        ]);

        $definition = $container->getDefinition('symfony_djot.converter.default');
        $cacheRef = $definition->getArgument('$cache');
        $this->assertNotNull($cacheRef);
        $this->assertInstanceOf(Reference::class, $cacheRef);
        $this->assertSame('cache.app', (string)$cacheRef);
    }

    public function testTwigExtensionIsRegistered(): void
    {
        $container = $this->createContainer([]);

        $this->assertTrue($container->hasDefinition('symfony_djot.twig_extension'));
        $definition = $container->getDefinition('symfony_djot.twig_extension');
        $this->assertTrue($definition->hasTag('twig.extension'));
    }

    public function testFormTypeIsRegistered(): void
    {
        $container = $this->createContainer([]);

        $this->assertTrue($container->hasDefinition('symfony_djot.form.type.djot'));
        $definition = $container->getDefinition('symfony_djot.form.type.djot');
        $this->assertTrue($definition->hasTag('form.type'));
    }

    public function testValidatorIsRegistered(): void
    {
        $container = $this->createContainer([]);

        $this->assertTrue($container->hasDefinition('symfony_djot.validator.valid_djot'));
        $definition = $container->getDefinition('symfony_djot.validator.valid_djot');
        $this->assertTrue($definition->hasTag('validator.constraint_validator'));
    }

    public function testMultipleConvertersAreRegistered(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => ['safe_mode' => false],
                'user_content' => ['safe_mode' => true],
                'docs' => ['safe_mode' => false],
            ],
        ]);

        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default'));
        $this->assertTrue($container->hasDefinition('symfony_djot.converter.user_content'));
        $this->assertTrue($container->hasDefinition('symfony_djot.converter.docs'));
    }

    public function testAutolinkExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        ['type' => 'autolink', 'allowed_schemes' => ['https']],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default.extension.0'));
        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame(['https'], $extDef->getArgument('$allowedSchemes'));
    }

    public function testExternalLinksExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'external_links',
                            'internal_hosts' => ['example.com'],
                            'target' => '_blank',
                            'rel' => 'noopener',
                            'nofollow' => true,
                        ],
                    ],
                ],
            ],
        ]);

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame(['example.com'], $extDef->getArgument('$internalHosts'));
        $this->assertSame('_blank', $extDef->getArgument('$target'));
        $this->assertSame('noopener', $extDef->getArgument('$rel'));
        $this->assertTrue($extDef->getArgument('$nofollow'));
    }

    public function testHeadingPermalinksExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'heading_permalinks',
                            'symbol' => '¶',
                            'position' => 'before',
                            'class' => 'anchor',
                            'aria_label' => 'Link',
                        ],
                    ],
                ],
            ],
        ]);

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame('¶', $extDef->getArgument('$symbol'));
        $this->assertSame('before', $extDef->getArgument('$position'));
        $this->assertSame('anchor', $extDef->getArgument('$cssClass'));
        $this->assertSame('Link', $extDef->getArgument('$ariaLabel'));
    }

    public function testMentionsExtension(): void
    {
        $container = $this->createContainer([
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

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame('/users/{username}', $extDef->getArgument('$urlTemplate'));
        $this->assertSame('mention', $extDef->getArgument('$cssClass'));
    }

    public function testSmartQuotesExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        ['type' => 'smart_quotes', 'locale' => 'fr'],
                    ],
                ],
            ],
        ]);

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame('fr', $extDef->getArgument('$locale'));
    }

    public function testTableOfContentsExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'table_of_contents',
                            'min_level' => 2,
                            'max_level' => 4,
                            'toc_class' => 'toc',
                        ],
                    ],
                ],
            ],
        ]);

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame(2, $extDef->getArgument('$minLevel'));
        $this->assertSame(4, $extDef->getArgument('$maxLevel'));
        $this->assertSame('toc', $extDef->getArgument('$cssClass'));
    }

    public function testWikilinksExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'wikilinks',
                            'url_template' => '/wiki/{page}',
                            'link_class' => 'wiki',
                        ],
                    ],
                ],
            ],
        ]);

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertNotNull($extDef->getFactory());
    }

    public function testDefaultAttributesExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        [
                            'type' => 'default_attributes',
                            'defaults' => [
                                'image' => ['loading' => 'lazy'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame(['image' => ['loading' => 'lazy']], $extDef->getArgument('$defaults'));
    }

    public function testFrontmatterExtension(): void
    {
        $container = $this->createContainer([
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

        $extDef = $container->getDefinition('symfony_djot.converter.default.extension.0');
        $this->assertSame('toml', $extDef->getArgument('$defaultFormat'));
        $this->assertTrue($extDef->getArgument('$renderAsComment'));
    }

    public function testSemanticSpanExtension(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        ['type' => 'semantic_span'],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default.extension.0'));
    }

    public function testUnknownExtensionIsIgnored(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        ['type' => 'unknown_extension'],
                    ],
                ],
            ],
        ]);

        $this->assertFalse($container->hasDefinition('symfony_djot.converter.default.extension.0'));
    }

    public function testMultipleExtensionsOnConverter(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => [
                    'extensions' => [
                        ['type' => 'autolink'],
                        ['type' => 'smart_quotes'],
                        ['type' => 'heading_permalinks'],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default.extension.0'));
        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default.extension.1'));
        $this->assertTrue($container->hasDefinition('symfony_djot.converter.default.extension.2'));

        $converterDef = $container->getDefinition('symfony_djot.converter.default');
        $extensions = $converterDef->getArgument('$extensions');
        $this->assertIsArray($extensions);
        $this->assertCount(3, $extensions);
    }

    public function testTwigExtensionReceivesAllConverters(): void
    {
        $container = $this->createContainer([
            'converters' => [
                'default' => ['safe_mode' => false],
                'safe' => ['safe_mode' => true],
            ],
        ]);

        $twigDef = $container->getDefinition('symfony_djot.twig_extension');
        $converters = $twigDef->getArgument('$converters');
        $this->assertIsArray($converters);
        $this->assertArrayHasKey('default', $converters);
        $this->assertArrayHasKey('safe', $converters);
    }

    public function testCreateWikilinksUrlGenerator(): void
    {
        $extension = SymfonyDjotExtension::createWikilinksUrlGenerator(
            '/wiki/{page}',
            'wiki-link',
        );

        $this->assertInstanceOf(WikilinksExtension::class, $extension);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createContainer(array $config): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $extension = new SymfonyDjotExtension();
        $extension->load([$config], $container);

        return $container;
    }
}
