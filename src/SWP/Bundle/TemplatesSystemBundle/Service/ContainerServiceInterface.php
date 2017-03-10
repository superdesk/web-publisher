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

namespace SWP\Bundle\TemplatesSystemBundle\Service;

use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

/**
 * Interface ContainerServiceInterface.
 */
interface ContainerServiceInterface
{
    /**
     * @param string                  $name
     * @param array                   $parameters
     * @param ContainerInterface|null $container
     *
     * @return ContainerInterface
     */
    public function createContainer($name, array $parameters = [], ContainerInterface $container = null): ContainerInterface;

    /**
     * @param ContainerInterface $container
     * @param array              $extraData
     *
     * @return ContainerInterface
     */
    public function updateContainer(ContainerInterface $container, array $extraData): ContainerInterface;
}
