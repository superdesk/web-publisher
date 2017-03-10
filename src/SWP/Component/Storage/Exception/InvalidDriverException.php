<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Storage\Exception;

class InvalidDriverException extends \InvalidArgumentException
{
    public function __construct($driver)
    {
        parent::__construct(sprintf('%s is not valid driver.', $driver));
    }
}
