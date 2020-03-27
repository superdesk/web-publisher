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

class GzipArchiver implements ArchiverInterface
{
    public function unarchive(string $source, string $target): bool
    {
        $file = gzopen($source, 'rb');
        $output = fopen($target, 'wb');

        while (!gzeof($file)) {
            if (false === fwrite($output, gzread($file, 4096))) {
                return false;
            }
        }

        fclose($output);
        gzclose($file);

        return true;
    }
}
