<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\StorageBundle\DependencyInjection\Driver;

use SWP\Component\Storage\DependencyInjection\Driver\PersistenceDriverInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDriver implements PersistenceDriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        foreach ($config['classes'] as $key => $classConfig) {
            $classConfig['name'] = $key;
            $this->setParameters($container, $classConfig);
            $this->createObjectManagerAlias($container, $classConfig);
            $this->createRepositoryDefinition($container, $classConfig);
            if (isset($classConfig['factory'])) {
                $this->createFactoryDefinition($container, $classConfig);
            }
        }
    }

    private function setParameters(ContainerBuilder $container, array $config)
    {
        if (isset($config['model'])) {
            $container->setParameter(sprintf('%s.model.%s.class', 'swp', $config['name']), $config['model']);
        }

        if (isset($config['repository'])) {
            $container->setParameter(sprintf('%s.repository.%s.class', 'swp', $config['name']), $config['repository']);
        }

        if (isset($config['factory'])) {
            $container->setParameter(sprintf('%s.factory.%s.class', 'swp', $config['name']), $config['factory']);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createRepositoryDefinition(ContainerBuilder $container, $config)
    {
        $repositoryClass = $this->getDriverRepositoryParameter();

        if (isset($config['repository'])) {
            $repositoryClass = $config['repository'];
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments([
            new Reference($this->getObjectManagerId()),
            $this->getClassMetadataDefinition($config),
        ]);

        $container->setDefinition('swp.repository.'.$config['name'], $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function createFactoryDefinition(ContainerBuilder $container, $config)
    {
        $factoryClass = $config['factory'];
        $definition = new Definition($factoryClass);
        $definition->setArguments([
            new Reference($this->getObjectManagerId()),
            $this->getClassMetadataDefinition($config),
        ]);

        $factoryClass = $config['factory'];
        $modelClass = $config['model'];
        $definition = new Definition($factoryClass);
        $definition->setArguments([$modelClass]);
        $container->setDefinition('swp.factory.'.$config['name'], $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function createObjectManagerAlias(ContainerBuilder $container, $config)
    {
        $container->setAlias(
            'swp.object_manager.'.$config['name'],
            new Alias($this->getObjectManagerId())
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataDefinition(array $config)
    {
        $definition = new Definition($this->getClassMetadataClassName());
        $definition
            ->setFactory([new Reference($this->getObjectManagerId()), 'getClassMetadata'])
            ->setArguments([$config['model']])
            ->setPublic(false)
        ;

        return $definition;
    }
}
