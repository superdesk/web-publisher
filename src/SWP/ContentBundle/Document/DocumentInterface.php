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

interface DocumentInterface
{
    /**
     * Gets the document identifier
     *
     * @return mixed
     */
    public function getId();

    /**
     * Gets the version of the document
     *
     * @return VersionInterface Returns version object
     */
    public function getVersion();

    /**
     * Gets the locale for the current document
     *
     * @return LocaleInterface Returns the locale object
     */
    public function getLocale();

    /**
     * Checks if the document has any translatable properties
     *
     * @return boolean
     */
    public function isTranslatable();

}
