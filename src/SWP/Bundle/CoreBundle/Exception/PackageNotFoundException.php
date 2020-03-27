<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Exception;

use Exception;
use RuntimeException;

class PackageNotFoundException extends RuntimeException
{
    public function __construct($name, Exception $previousException = null)
    {
        parent::__construct("Package '$name' could not be found!", 404, $previousException);
    }
}
