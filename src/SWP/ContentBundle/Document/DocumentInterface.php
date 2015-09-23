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
    public function getUuid();

    /**
     * Checks if the document is transtable, implements DocumentTranslatableInterface
     *
     * @return boolean
     */
    public function isTranslatable();

    /**
     * Checks if the document is versionable, implements DocumentVersionableInterface
     *
     * @return boolean
     */
    public function isVersionalble();
}
