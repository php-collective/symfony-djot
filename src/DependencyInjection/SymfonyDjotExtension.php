<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\DependencyInjection;

use Djot\Extension\AutolinkExtension;
use Djot\Extension\CodeGroupExtension;
use Djot\Extension\DefaultAttributesExtension;
use Djot\Extension\ExternalLinksExtension;
use Djot\Extension\FrontmatterExtension;
use Djot\Extension\HeadingPermalinksExtension;
use Djot\Extension\MentionsExtension;
use Djot\Extension\SemanticSpanExtension;
use Djot\Extension\SmartQuotesExtension;
use Djot\Extension\TableOfContentsExtension;
use Djot\Extension\WikilinksExtension;
use PhpCollective\SymfonyDjot\Form\Type\DjotType;
use PhpCollective\SymfonyDjot\Service\DjotConverter;
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use PhpCollective\SymfonyDjot\Twig\DjotExtension;
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjotValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class SymfonyDjotExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $converterReferences = [];

        foreach ($config['converters'] as $name => $converterConfig) {
            $serviceId = 'symfony_djot.converter.' . $name;

            // Create extension service definitions
            $extensionRefs = [];
            $extensionConfigs = $converterConfig['extensions'] ?? [];
            foreach ($extensionConfigs as $index => $extConfig) {
                $extServiceId = $serviceId . '.extension.' . $index;
                $extDefinition = $this->createExtensionDefinition($extConfig);
                if ($extDefinition !== null) {
                    $container->setDefinition($extServiceId, $extDefinition);
                    $extensionRefs[] = new Reference($extServiceId);
                }
            }

            $definition = new Definition(DjotConverter::class);
            $definition->setArgument('$safeMode', $converterConfig['safe_mode']);

            if ($config['cache']['enabled']) {
                $definition->setArgument('$cache', new Reference($config['cache']['pool']));
            } else {
                $definition->setArgument('$cache', null);
            }

            $definition->setArgument('$extensions', $extensionRefs);

            $container->setDefinition($serviceId, $definition);
            $converterReferences[$name] = new Reference($serviceId);

            if ($name === 'default') {
                $container->setAlias(DjotConverterInterface::class, $serviceId);
                $container->setAlias('symfony_djot.converter', $serviceId);
            }
        }

        $twigExtension = new Definition(DjotExtension::class);
        $twigExtension->setArgument('$converters', $converterReferences);
        $twigExtension->addTag('twig.extension');
        $container->setDefinition('symfony_djot.twig_extension', $twigExtension);

        $formType = new Definition(DjotType::class);
        $formType->addTag('form.type');
        $container->setDefinition('symfony_djot.form.type.djot', $formType);

        $validator = new Definition(ValidDjotValidator::class);
        $validator->addTag('validator.constraint_validator');
        $container->setDefinition('symfony_djot.validator.valid_djot', $validator);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createExtensionDefinition(array $config): ?Definition
    {
        $type = $config['type'] ?? null;

        return match ($type) {
            'autolink' => $this->createAutolinkExtension($config),
            'code_group' => $this->createCodeGroupExtension($config),
            'default_attributes' => $this->createDefaultAttributesExtension($config),
            'external_links' => $this->createExternalLinksExtension($config),
            'frontmatter' => $this->createFrontmatterExtension($config),
            'heading_permalinks' => $this->createHeadingPermalinksExtension($config),
            'mentions' => $this->createMentionsExtension($config),
            'semantic_span' => $this->createSemanticSpanExtension(),
            'smart_quotes' => $this->createSmartQuotesExtension($config),
            'table_of_contents' => $this->createTableOfContentsExtension($config),
            'wikilinks' => $this->createWikilinksExtension($config),
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createAutolinkExtension(array $config): Definition
    {
        $definition = new Definition(AutolinkExtension::class);

        if (!empty($config['allowed_schemes'])) {
            $definition->setArgument('$allowedSchemes', $config['allowed_schemes']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createCodeGroupExtension(array $config): Definition
    {
        $definition = new Definition(CodeGroupExtension::class);

        if (isset($config['wrapper_class'])) {
            $definition->setArgument('$wrapperClass', $config['wrapper_class']);
        }
        if (isset($config['panel_class'])) {
            $definition->setArgument('$panelClass', $config['panel_class']);
        }
        if (isset($config['label_class'])) {
            $definition->setArgument('$labelClass', $config['label_class']);
        }
        if (isset($config['radio_class'])) {
            $definition->setArgument('$radioClass', $config['radio_class']);
        }
        if (isset($config['id_prefix'])) {
            $definition->setArgument('$idPrefix', $config['id_prefix']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createDefaultAttributesExtension(array $config): Definition
    {
        $definition = new Definition(DefaultAttributesExtension::class);

        if (!empty($config['defaults'])) {
            $definition->setArgument('$defaults', $config['defaults']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createExternalLinksExtension(array $config): Definition
    {
        $definition = new Definition(ExternalLinksExtension::class);

        if (!empty($config['internal_hosts'])) {
            $definition->setArgument('$internalHosts', $config['internal_hosts']);
        }
        if (isset($config['target'])) {
            $definition->setArgument('$target', $config['target']);
        }
        if (isset($config['rel'])) {
            $definition->setArgument('$rel', $config['rel']);
        }
        if (isset($config['nofollow'])) {
            $definition->setArgument('$nofollow', $config['nofollow']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createFrontmatterExtension(array $config): Definition
    {
        $definition = new Definition(FrontmatterExtension::class);

        if (isset($config['default_format'])) {
            $definition->setArgument('$defaultFormat', $config['default_format']);
        }
        if (isset($config['render_as_comment'])) {
            $definition->setArgument('$renderAsComment', $config['render_as_comment']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createHeadingPermalinksExtension(array $config): Definition
    {
        $definition = new Definition(HeadingPermalinksExtension::class);

        if (isset($config['symbol'])) {
            $definition->setArgument('$symbol', $config['symbol']);
        }
        if (isset($config['position'])) {
            $definition->setArgument('$position', $config['position']);
        }
        if (isset($config['class'])) {
            $definition->setArgument('$cssClass', $config['class']);
        }
        if (isset($config['aria_label'])) {
            $definition->setArgument('$ariaLabel', $config['aria_label']);
        }
        if (isset($config['show_on_hover'])) {
            $definition->setArgument('$showOnHover', $config['show_on_hover']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createMentionsExtension(array $config): Definition
    {
        $definition = new Definition(MentionsExtension::class);

        if (isset($config['user_url_template'])) {
            $definition->setArgument('$urlTemplate', $config['user_url_template']);
        }
        if (isset($config['user_class'])) {
            $definition->setArgument('$cssClass', $config['user_class']);
        }

        return $definition;
    }

    private function createSemanticSpanExtension(): Definition
    {
        return new Definition(SemanticSpanExtension::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createSmartQuotesExtension(array $config): Definition
    {
        $definition = new Definition(SmartQuotesExtension::class);

        if (isset($config['locale'])) {
            $definition->setArgument('$locale', $config['locale']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createTableOfContentsExtension(array $config): Definition
    {
        $definition = new Definition(TableOfContentsExtension::class);

        if (isset($config['min_level'])) {
            $definition->setArgument('$minLevel', $config['min_level']);
        }
        if (isset($config['max_level'])) {
            $definition->setArgument('$maxLevel', $config['max_level']);
        }
        if (isset($config['toc_class'])) {
            $definition->setArgument('$cssClass', $config['toc_class']);
        }

        return $definition;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createWikilinksExtension(array $config): Definition
    {
        $definition = new Definition(WikilinksExtension::class);

        if (isset($config['url_template'])) {
            // Create a closure factory for the URL generator
            $template = $config['url_template'];
            $definition->setFactory([self::class, 'createWikilinksUrlGenerator']);
            $definition->setArguments([
                $template,
                $config['link_class'] ?? 'wikilink',
            ]);
        } else {
            if (isset($config['link_class'])) {
                $definition->setArgument('$cssClass', $config['link_class']);
            }
        }

        return $definition;
    }

    /**
     * Factory method for creating WikilinksExtension with URL template.
     */
    public static function createWikilinksUrlGenerator(string $template, string $cssClass): WikilinksExtension
    {
        $urlGenerator = static function (string $page) use ($template): string {
            $slug = strtolower(trim($page));
            $slug = (string)preg_replace('/\s+/', '-', $slug);
            $slug = (string)preg_replace('/[^a-z0-9\-_\/]/', '', $slug);
            $slug = (string)preg_replace('/-+/', '-', $slug);

            return str_replace('{page}', $slug, $template);
        };

        return new WikilinksExtension($urlGenerator, $cssClass);
    }
}
