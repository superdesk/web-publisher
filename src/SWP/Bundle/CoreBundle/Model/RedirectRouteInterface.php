<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RedirectRouteInterface as BaseRedirectRouteInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

interface RedirectRouteInterface extends BaseRedirectRouteInterface, TenantAwareInterface
{
    public function setPermanent(bool $pernament);

    public function setUri(string $string);

    public function setRouteName(string $routeName);

    public function setStaticPrefix($prefix);
}
