<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle;

use SWP\Component\Storage\Bundle\Bundle;
use SWP\Component\Storage\Drivers;

class SWPContentBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            Drivers::DRIVER_DOCTRINE_PHPCR_ODM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespaces()
    {
        return [
            $this->getConfigFilesPath('model') => 'SWP\Bundle\ContentBundle\Model',
            $this->getConfigFilesPath(Drivers::DRIVER_DOCTRINE_PHPCR_ODM) => 'SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR',
        ];
    }
}
