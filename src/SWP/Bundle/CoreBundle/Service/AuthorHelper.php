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

namespace SWP\Bundle\CoreBundle\Service;

final class AuthorHelper
{
    private function __construct()
    {
    }

    public static function authorsToIds(array $authors): array
    {
        $authorIds = [];
        foreach ($authors as $author) {
            if (!isset($author['id'])) {
                continue;
            }

            $authorIds[] = $author['id'];
        }

        return $authorIds;
    }
}
