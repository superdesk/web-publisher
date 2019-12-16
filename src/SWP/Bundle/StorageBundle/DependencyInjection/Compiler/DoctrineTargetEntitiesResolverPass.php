<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú., Paweł Jędrzejewski and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú, Paweł Jędrzejewski
 * @license http://www.superdesk.org/license
 * @license http://docs.sylius.org/en/latest/contributing/code/license.html
 */

namespace SWP\Bundle\StorageBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Resolves given target entities with container parameters.
 * Usable only with *doctrine/orm* driver.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DoctrineTargetEntitiesResolverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $resources = $container->getParameter('swp.resources');
            $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        $interfaces = $this->getInterfacesMapping($resources);
        foreach ($interfaces as $interface => $model) {
            $resolveTargetEntityListener->addMethodCall('addResolveTargetEntity', [
                $this->getInterface($container, $interface),
                $this->getClass($container, $model),
                [],
            ]);
        }

        if (!$resolveTargetEntityListener->hasTag('doctrine.event_listener')) {
            $resolveTargetEntityListener->addTag('doctrine.event_listener', ['event' => Events::loadClassMetadata]);
        }
    }

    /**
     * @return array
     */
    private function getInterfacesMapping(array $resources)
    {
        $interfaces = [];
        foreach ($resources as $alias => $configuration) {
            if (isset($configuration['interface'])) {
                $alias = explode('.', $alias);

                if (!isset($alias[0], $alias[1])) {
                    throw new \RuntimeException(sprintf('Error configuring "%s" resource. The resource alias should follow the "prefix.name" format.', $alias[0]));
                }

                $interfaces[$configuration['interface']] = sprintf('%s.model.%s.class', $alias[0], $alias[1]);
            }
        }

        return $interfaces;
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getInterface(ContainerBuilder $container, $key)
    {
        if ($container->hasParameter($key)) {
            return $container->getParameter($key);
        }

        if (interface_exists($key)) {
            return $key;
        }

        throw new \InvalidArgumentException(sprintf('The interface %s does not exist.', $key));
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getClass(ContainerBuilder $container, $key)
    {
        if ($container->hasParameter($key)) {
            return $container->getParameter($key);
        }

        if (class_exists($key)) {
            return $key;
        }

        throw new \InvalidArgumentException(sprintf('The class %s does not exist.', $key));
    }
}
