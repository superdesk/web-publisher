<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Exception;

class UnexpectedTypeException extends \InvalidArgumentException
{
    /**
     * @param string $type
     * @param string $expectedType
     *
     * @return UnexpectedTypeException
     */
    public static function unexpectedType(string $type, string $expectedType): self
    {
        return new self(sprintf(
            'Expected argument of type "%s", "%s" given.',
            $expectedType,
            $type
        ));
    }
}
