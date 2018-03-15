<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Bundle\CoreBundle\Model\PackageInterface;

interface PublishDestinationProviderInterface
{
    /**
     * @param PackageInterface $package
     *
     * @return array
     */
    public function getDestinations(PackageInterface $package): array;

    /**
     * @param PackageInterface $package
     *
     * @return int
     */
    public function countDestinations(PackageInterface $package): int;
}
