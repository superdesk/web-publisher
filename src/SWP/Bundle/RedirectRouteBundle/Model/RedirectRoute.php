<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Redirect Route Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RedirectRouteBundle\Model;

use SWP\Component\Common\Model\TimestampableTrait;
use Symfony\Cmf\Bundle\RoutingBundle\Model\RedirectRoute as BaseRedirectRoute;

class RedirectRoute extends BaseRedirectRoute implements RedirectRouteInterface
{
    use TimestampableTrait;
}
