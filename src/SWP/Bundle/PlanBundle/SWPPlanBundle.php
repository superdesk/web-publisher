<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Plan Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\PlanBundle;

use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;

class SWPPlanBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            Drivers::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModelClassNamespace()
    {
        return 'SWP\Component\Plan\Model';
    }
}
