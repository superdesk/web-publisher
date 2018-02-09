<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Theme\Uploader;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

interface ThemeLogoUploaderInterface
{
    const THEME_LOGO_UPLOAD_SUBDIR = 'logos';

    /**
     * @param ThemeInterface $theme
     */
    public function upload(ThemeInterface $theme): void;

    /**
     * @param string $path
     *
     * @return string
     */
    public function getThemeLogoUploadPath(string $path): string;

    /**
     * @param string $path
     *
     * @return bool
     */
    public function remove(string $path): bool;
}
