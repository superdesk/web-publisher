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

namespace SWP\Bundle\CoreBundle\MessageHandler;

use SWP\Bundle\CoreBundle\Exception\PackageNotFoundException;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMigrationMessage;

class ContentPushMessageHandler extends AbstractContentPushHandler
{
    public function __invoke(ContentPushMigrationMessage $contentPushMessage)
    {
        $tenantId = $contentPushMessage->getTenantId();
        $packageId = $contentPushMessage->getPackageId();
        $package = $this->packageRepository->findOneBy(['id' => $packageId]);

        if (null === $package) {
            throw new PackageNotFoundException($packageId);
        }

        $this->execute($tenantId, $package);
    }
}
