<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Component\Storage\Factory\FactoryInterface;

class RouteFactory implements RouteFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $baseFactory;

    /**
     * RouteFactory constructor.
     *
     * @param FactoryInterface $baseFactory
     */
    public function __construct(FactoryInterface $baseFactory)
    {
        $this->baseFactory = $baseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->baseFactory->create();
    }
}
