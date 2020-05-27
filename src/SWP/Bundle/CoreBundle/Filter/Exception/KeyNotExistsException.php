<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Filter\Exception;

class KeyNotExistsException extends FilterException
{
    public function __construct(string $key, $currentDataKeys)
    {
        parent::__construct(sprintf('Provided key ("%s") does not exists. Possible options are: %s', $key, \json_encode($currentDataKeys)));
    }
}
