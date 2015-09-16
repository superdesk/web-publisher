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

namespace SWP\ContentBundle\Document;

/**
 * Version interface for documents
 */
interface VersionInterface
{
    /**
     * Gets the version in your format
     *
     * @return mixed
     */
    public function getVersion();

    /**
     * Method which checks if first version is greater then the second version.
     *
     * @param  VersionInterface $version1 Version to be compared
     * @param  VersionInterface $version2 Version to be compared with
     *
     * @return boolean
     */
    public static function greaterThan(
        VersionInterface $version1,
        VersionInterface $version2
    );
}
