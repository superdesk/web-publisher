<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\File;

final class FileExtensionChecker implements FileExtensionCheckerInterface
{
    public function isAudio(string $mimeType): bool
    {
        return $this->startsWith($mimeType, 'audio');
    }

    public function isVideo(string $mimeType): bool
    {
        return $this->startsWith($mimeType, 'video');
    }

    public function isImage(string $mimeType): bool
    {
        return $this->startsWith($mimeType, 'image');
    }

    private function startsWith(string $mimeType, string $string): bool
    {
        return 0 === strpos($mimeType, $string);
    }
}
