<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\WebRendererBundle\Version;

use SWP\UpdaterBundle\Version\VersionInterface;

/**
 * Application version class.
 */
final class Version implements VersionInterface
{
    /**
     * Version string.
     *
     * @var string
     */
    private $version = '0.0.1';

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

        return $this;
    }
}
