<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\DependencyInjection;

use PhpCollective\SymfonyDjot\Service\DjotConverter;
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use PhpCollective\SymfonyDjot\Twig\DjotExtension;
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

            $definition = new Definition(DjotConverter::class);
            $definition->setArgument('$safeMode', $converterConfig['safe_mode']);

            if ($config['cache']['enabled']) {
                $definition->setArgument('$cache', new Reference($config['cache']['pool']));
            } else {
                $definition->setArgument('$cache', null);
            }

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
    }
}
