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

use SWP\ContentBundle\Storage\StorageInterface;

abstract class AbstractManager implements ManagerInterface
{
    /**
     * Storage service
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Version object namespace
     *
     * @var string
     */
    protected $version;

    /**
     * Locale object namespace
     *
     * @var string
     */
    protected $locale;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        StorageInterface $storage,
        $versionNamespace
        $localeNamespace,
    ) {
        $this->storage = $storage;
        $this->version = $versionNamespace;
        $this->locale = $localeNamespace;
    }
}
