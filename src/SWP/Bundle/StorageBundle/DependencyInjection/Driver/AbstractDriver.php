<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\DependencyInjection\Driver;

use Doctrine\Persistence\ObjectManager;
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
        $this->setParameters($container, $config);
        $this->createObjectManagerAlias($container, $config);
        $this->createRepositoryDefinition($container, $config);
        if (isset($config['factory'])) {
            $this->createFactoryDefinition($container, $config);
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
        $definition->setPublic(true);
        $definition->setArguments([
            new Reference($this->getObjectManagerId($config)),
            $this->getClassMetadataDefinition($config),
        ]);

        $container->setDefinition('swp.repository.'.$config['name'], $definition);

        // try to match interface with class
        $classNameArray = \explode('\\', $repositoryClass);
        foreach (class_implements($repositoryClass) as $interface) {
            if (false !== strpos($interface, '\\'.$classNameArray[array_key_last($classNameArray)].'Interface')) {
                $container->setAlias($interface, 'swp.repository.'.$config['name']);

                break;
            }
        }

        foreach (class_implements($repositoryClass) as $typehintClass) {
            $container->registerAliasForArgument(
                'swp.repository.'.$config['name'],
                $typehintClass,
                $config['name'].' repository'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createFactoryDefinition(ContainerBuilder $container, $config)
    {
        $factoryClass = $config['factory'];
        $modelClass = $config['model'];
        $definition = new Definition($factoryClass);
        $definition->setPublic(true);
        $definition->setArguments([$modelClass]);
        $container->setDefinition('swp.factory.'.$config['name'], $definition);

        // try to match interface with class
        $classNameArray = \explode('\\', $factoryClass);
        foreach (class_implements($factoryClass) as $interface) {
            if (false !== strpos($interface, '\\'.$classNameArray[array_key_last($classNameArray)].'Interface')) {
                $container->setAlias($interface, 'swp.factory.'.$config['name']);

                break;
            }
        }

        foreach (class_implements($factoryClass) as $typehintClass) {
            $container->registerAliasForArgument(
                'swp.factory.'.$config['name'],
                $typehintClass,
                $config['name'].' factory'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createObjectManagerAlias(ContainerBuilder $container, $config)
    {
        $aliasDefinition = $container->setAlias(
            'swp.object_manager.'.$config['name'],
            new Alias($this->getObjectManagerId($config))
        );
        $aliasDefinition->setPublic(true);

        $container->registerAliasForArgument(
            'swp.object_manager.'.$config['name'],
            ObjectManager::class,
            $config['name'].' manager'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectManagerName($config)
    {
        $objectManagerName = null;

        if (isset($config['object_manager_name'])) {
            $objectManagerName = $config['object_manager_name'];
        }

        return $objectManagerName;
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataDefinition(array $config)
    {
        $definition = new Definition($this->getClassMetadataClassName());
        $definition
            ->setFactory([new Reference($this->getObjectManagerId($config)), 'getClassMetadata'])
            ->setArguments([$config['model']])
            ->setPublic(false)
        ;

        return $definition;
    }
}
