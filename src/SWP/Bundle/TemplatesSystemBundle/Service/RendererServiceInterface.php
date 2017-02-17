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

/**
 * Interface RendererServiceInterface.
 */
interface RendererServiceInterface
{
    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $createIfNotExists
     *
     * @return ContainerRenderer
     *
     * @throws \Exception
     */
    public function getContainerRenderer($name, array $parameters = [], $createIfNotExists = true);
}
