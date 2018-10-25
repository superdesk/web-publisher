<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Routing\Candidates;

use Symfony\Cmf\Component\Routing\Candidates\Candidates as BaseCandidates;

final class Candidates extends BaseCandidates
{
    public const PUBLISHER_API_ROUTE_PREFIX = 'swp_api';

    public function isCandidate($name): bool
    {
        return false === strpos($name, self::PUBLISHER_API_ROUTE_PREFIX);
    }
}
