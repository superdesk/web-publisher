<?php

namespace SWP\Bundle\ContentBundle\DependencyInjection\Driver;

use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PHPCRDriver extends AbstractDriver
{
    public static $type = 'phpcr';

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        foreach ($config['repositories'] as $key => $repositoryConfig) {
            $repositoryConfig['name'] = $key;
            $this->createObjectManagerAlias($container, $repositoryConfig);
            $this->createRepositoryDefinition($container, $repositoryConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectManagerId()
    {
        return 'doctrine_phpcr.odm.document_manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadataClassName()
    {
        return ClassMetadata::class;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($type)
    {
        return self::$type === $type;
    }
}
