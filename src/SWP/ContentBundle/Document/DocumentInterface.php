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

class DocumentInterface
{
    /**
     * Gets the document identifier
     *
     * @return string
     */
    public function getId();

    /**
     * Gets the version of the document
     *
     * @return int|null Returns version as integer or null whem not used
     */
    public function getVersion();

    /**
     * Gets the locale for the current document
     *
     * @return string Returns the locale in string format
     */
    public function getLocale();

    /**
     * Checks if the document has any translatable properties
     *
     * @return boolean
     */
    public function isTranslatable();

}
