<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Manager;

use SWP\Bundle\CoreBundle\Manager\MediaManager as BaseMediaManager;

final class SeoMediaManager extends BaseMediaManager
{
    protected function getMediaBasePath(): string
    {
        $tenant = $this->tenantContext->getTenant();
        $pathElements = ['swp', $tenant->getOrganization()->getCode(), 'seo_images'];

        return implode('/', $pathElements);
    }
}
