<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\CoreBundle\Model\CompositePublishActionInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;

interface ArticlePublisherInterface
{
    /**
     * @param PackageInterface                $package
     * @param CompositePublishActionInterface $action
     */
    public function publish(PackageInterface $package, CompositePublishActionInterface $action);

    /**
     * @param PackageInterface $package
     * @param array            $tenants
     */
    public function unpublish(PackageInterface $package, array $tenants = []);
}
