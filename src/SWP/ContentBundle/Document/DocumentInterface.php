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
     * Gets the UUID
     *
     * @return mixed
     */
    // TODO: Should we enforce this or not?
    // public function getUuid();

    /**
     * Checks if the document is transtable, implements TranslatableDocumentInterface
     *
     * @return boolean
     */
    public function isTranslatable();

    /**
     * Checks if the document is versionable, implements VersionableDocumentInterface
     *
     * @return boolean
     */
    public function isVersionable();

    /**
     * Get the creation date and time
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Get the date and time of last modification
     *
     * @return \DateTime|null Returns null only when never edited
     */
    public function getLastModified();
}
