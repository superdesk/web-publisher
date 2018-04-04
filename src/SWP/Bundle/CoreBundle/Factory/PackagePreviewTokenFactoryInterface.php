<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Factory;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\PackagePreviewTokenInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

interface PackagePreviewTokenFactoryInterface extends FactoryInterface
{
    /**
     * @param RouteInterface $route
     * @param string         $body
     *
     * @return PackagePreviewTokenInterface
     */
    public function createTokenizedWith(RouteInterface $route, string $body): PackagePreviewTokenInterface;
}
