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

use SWP\Bundle\StorageBundle\DependencyInjection\Driver\AbstractDriver;
use Symfony\Component\DependencyInjection\Parameter;

class ORMDriver extends AbstractDriver
{
    public static $type = 'orm';

    /**
     * {@inheritdoc}
     */
    public function getObjectManagerId()
    {
        return 'doctrine.orm.default_entity_manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadataClassName()
    {
        return '\\Doctrine\\ORM\\Mapping\\ClassMetadata';
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
        return new Parameter('swp.orm.repository.class');
    }
}
