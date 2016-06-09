<?php

namespace SWP\Bundle\ContentBundle\DependencyInjection\Driver;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDriver implements PersistenceDriverInterface
{
    protected function createRepositoryDefinition(ContainerBuilder $container, $config)
    {
        $repositoryClass = new Parameter('swp.phpcr_odm.repository.class');

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

    protected function createObjectManagerAlias(ContainerBuilder $container, $config)
    {
        $container->setAlias(
            'swp.manager.'.$config['name'].'.default',
            new Alias($this->getObjectManagerId())
        );
    }

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
