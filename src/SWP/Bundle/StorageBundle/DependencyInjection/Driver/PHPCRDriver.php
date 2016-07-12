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

use Symfony\Component\DependencyInjection\Parameter;

class PHPCRDriver extends AbstractDriver
{
    public static $type = 'phpcr';

    /**
     * {@inheritdoc}
     */
    public function getObjectManagerId(array $config)
    {
        if (null !== $name = $this->getObjectManagerName($config)) {
            return sprintf('doctrine_phpcr.odm.%s_document_manager', $name);
        }

        return 'doctrine_phpcr.odm.document_manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadataClassName()
    {
        return '\\Doctrine\\ODM\\PHPCR\\Mapping\\ClassMetadata';
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($type)
    {
        return self::$type === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverRepositoryParameter()
    {
        return new Parameter('swp.phpcr_odm.repository.class');
    }
}
