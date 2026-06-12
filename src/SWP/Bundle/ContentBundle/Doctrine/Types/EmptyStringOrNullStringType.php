<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * Stores empty strings as NULL and hydrates NULL back to an empty string.
 *
 * Needed for Route::$staticPrefix: the typed CMF Route property defaults to
 * an empty string, but the (staticprefix, tenant_code) unique index relies
 * on "unset" prefixes being NULL so they never collide.
 */
class EmptyStringOrNullStringType extends StringType
{
    public const NAME = 'empty_string_or_null_string';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): string
    {
        return null === $value ? '' : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
