<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle;

class Drivers
{
    const DRIVER_DOCTRINE_ORM = 'orm';
    const DRIVER_DOCTRINE_MONGODB_ODM = 'mongodb';
    const DRIVER_DOCTRINE_PHPCR_ODM = 'phpcr';
}
