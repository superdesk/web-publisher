<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Manager;

/**
 * Interface for all Managers in the content bundle
 */
class ManagerInterface
{
    /**
     * Construct for Manager services
     *
     * @param StorageInterface $storage Storage implementation
     * @param string $localeNamespace Namespace of the locale object
     * @param string $versionNamespace Namespace of the version object
     */
    public function __construct(
        StorageInterface $storage,
        $localeNamespace,
        $versionNamespace
    );
}
