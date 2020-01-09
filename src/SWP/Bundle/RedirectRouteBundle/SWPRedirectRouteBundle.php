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

namespace SWP\Bundle\RedirectRouteBundle;

use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;

class SWPRedirectRouteBundle extends Bundle
{
    public function getSupportedDrivers(): array
    {
        return [
            Drivers::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function getModelClassNamespace(): string
    {
        return 'SWP\Bundle\RedirectRouteBundle\Model';
    }
}
