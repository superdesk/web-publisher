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

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDriver implements PersistenceDriverInterface
{
    /**
     * {@inheritdoc}
     */
    protected function createRepositoryDefinition(ContainerBuilder $container, $config)
    {
        $repositoryClass = $this->getDriverRepositoryParameter();

        if (isset($config['class'])) {
            $repositoryClass = $config['class'];
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

    /**
     * {@inheritdoc}
     */
    public function getDriverRepositoryParameter()
    {
        return new Parameter('swp.phpcr_odm.repository.class');
    }
}
