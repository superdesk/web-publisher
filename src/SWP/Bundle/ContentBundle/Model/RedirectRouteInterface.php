<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Bundle\RedirectRouteBundle\Model\RedirectRouteInterface as BaseRedirectRouteInterface;

interface RedirectRouteInterface extends BaseRedirectRouteInterface
{
    public function getRouteSource(): ?RouteInterface;

    public function setRouteSource(?RouteInterface $route): void;
}
