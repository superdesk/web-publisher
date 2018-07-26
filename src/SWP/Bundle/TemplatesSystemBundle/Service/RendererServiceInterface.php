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

use SWP\Bundle\TemplatesSystemBundle\Container\ContainerRenderer;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

interface RendererServiceInterface
{
    public function getContainerRenderer(string $name, array $parameters = [], bool $createIfNotExists = true, ContainerInterface $container = null): ContainerRenderer;
}
