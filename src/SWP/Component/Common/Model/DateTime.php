<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Model;

final class DateTime
{
    private static $dateTime;

    public static function getCurrentDateTime(): \DateTimeInterface
    {
        if (null !== static::$dateTime) {
            return static::$dateTime;
        }

        return new \DateTime();
    }

    public static function setCurrentDateTime(\DateTimeInterface $dateTime): void
    {
        static::$dateTime = $dateTime;
    }

    public static function resetCurrentDateTime(): void
    {
        if (null !== static::$dateTime) {
            static::$dateTime = null;
        }
    }
}
