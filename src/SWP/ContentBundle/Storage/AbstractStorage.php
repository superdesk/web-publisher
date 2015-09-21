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

namespace SWP\ContentBundle\Storage;

/**
 * Abstract for library specific storage implementations
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Document manager
     *
     * @var mnixed Object of class that representes the document manager
     */
    protected $manager;

    /**
     * Whether versioning is supported in this storage implementation
     *
     * @var boolean
     */
    protected $supportsVersioning = false;

    /**
     * Whether locale handling is supported in this storage implementation
     *
     * @var boolean
     */
    protected $supportsLocale = false;

    /**
     * {@inheritdoc}
     */
    public function supportsVersioning()
    {
        return $this->supportsVersioning;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsLocale()
    {
        return $this->locale;
    }
}
