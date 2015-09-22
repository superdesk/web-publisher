<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Storage;

use InvalidArgumentException;

/**
 * Abstract for library specific storage implementations.
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Document manager.
     *
     * @var mnixed Object of class that representes the document manager
     */
    protected $manager;

    /**
     * Whether versioning is supported in this storage implementation.
     *
     * @var boolean
     */
    protected $supportsVersioning = false;

    /**
     * Whether locale handling is supported in this storage implementation.
     *
     * @var boolean
     */
    protected $supportsLocale = false;

    /**
     * Whether locking is supported by the storage implementation.
     *
     * @var boolean
     */
    protected $supportsLocking = false;

    /**
     * {@inheritdoc}
     */
    public function supportsVersioning()
    {
        return $this->supportsVersioning;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsLocale()
    {
        return $this->supportsLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsLocking()
    {
        return $this->supportsLocking
    }

    /**
     * Extracts the special parameters for orderby, order, limit, offset, etc.
     * from the $parameters. It does a strict type check for each value and
     * returns them as an array or throws an exception.
     *
     * @param  array $parameters Parameters argument from searchDocuments method
     *
     * @return array Returns array according to the following format:
     *     array(
     *         'orderby' => array('<fieldname>')|<fieldname>|null,
     *         'order' => array('asc|desc')|'asc|desc'|null,
     *         'orderfull' => array('<combination of orderby and order values>')|'<combination as well>',
     *         'limit' => [0-9]+|null,
     *         'offset' => [0-9]+|null
     *     )
     *
     * @throws \InvalidArgumentException Will be thrown when special parameter
     *     is set, but has invalid type.
     */
    protected function extractSpecialParameters($parameters)
    {
        $orderby = null;
        $order = 'asc';
        $limit = null;
        $offset = null;

        if (isset($parameters['orderby']) && !empty($parameters['orderby'])) {
            // TODO: Check if we need to implement InvalidArgumentException
            // TODO: Check if we need to do something special when this is an array
            $orderby = $parameters['orderby'];
        }

        if (isset($parameters['order']) && !is_null($parameters['order'])) {

            if (is_array($parameters['order'])) {
                foreach ($parameters['order'] as $order) {
                    if (!in_array(strtolower($order), array('asc', 'desc'))) {
                       throw new InvalidArgumentException('Invalid datatype for key order in first argument.');
                    }
                }
            } else {
                if (!in_array(strtolower($parameters['order']), array('asc', 'desc'))) {
                    throw new InvalidArgumentException('Invalid datatype for key order in first argument.');
                }
            }

            // TODO: Check if $orderby is array and then count and fill to same amount
            $order = $parameters['order'];
        }

        if (isset($parameters['limit']) && !is_null($parameters['limit'])) {
            if (!is_int($parameters['limit'])) {
                throw new InvalidArgumentException('Invalid datatype for key limit in first argument.');
            }
            $limit = $parameters['limit'];
        }

        if (isset($parameters['offset']) && !is_null($parameters['offset'])) {
            if (!is_int($parameters['offset'])) {
                throw new InvalidArgumentException('Invalid datatype for key offset in first argument.');
            }
            $offset = $parameters['offset'];
        }

        return array (
            'orderby' => $orderby,
            'order' => $order,
            'orderfull' => sprintf('%s %s', $orderby, $order),
            'limit' => $limit,
            'offset' => $offset,
        );
    }
}
