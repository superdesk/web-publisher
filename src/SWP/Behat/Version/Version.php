<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Behat\Version;

use SWP\Bundle\CoreBundle\Version\VersionInterface;

final class Version implements VersionInterface
{
    /**
     * Version string.
     *
     * @var string
     */
    private $version = '1-test';

    /**
     * Code name string.
     *
     * @var string
     */
    private $codeName = 'test';

    /**
     * Release date string.
     *
     * @var string
     */
    private $releaseDate = '2018-06-07';

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getCodeName(): string
    {
        return $this->codeName;
    }

    public function setCodeName(string $codeName): self
    {
        $this->codeName = $codeName;

        return $this;
    }

    public function getReleaseDate(): string
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(string $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }
}
