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

namespace SWP\ContentBundle\Document\Version;

class DefaultVersion implements VersionInterface
{
    /**
     * Version
     *
     * @var mixed
     */
    protected $version;

    /**
     * {@inheritdoc}
     */
    public function __construct($version)
    {
        $this->setVersion($version);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($version1, $version2)
    {
        return ($version1 > $version2);
    }
}
