<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Factory;

use SWP\Component\Storage\Factory\FactoryInterface;

class ContainerFactory
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
