<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Archiver Component.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Archiver\Archiver;

use ZipArchive;

class ZipArchiver implements ArchiverInterface
{
    private $zip;

    public function __construct()
    {
        $this->zip = new ZipArchive();
    }

    public function unarchive(string $source, string $target): bool
    {
        if (false === $this->zip->open($source)) {
            return false;
        }

        if (false === $this->zip->extractTo(dirname($target))) {
            $this->zip->close();

            return false;
        }

        return $this->zip->close();
    }
}
