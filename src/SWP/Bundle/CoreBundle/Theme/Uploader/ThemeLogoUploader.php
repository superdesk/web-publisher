<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Theme\Uploader;

use League\Flysystem\Filesystem;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

final class ThemeLogoUploader implements ThemeLogoUploaderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * ThemeLogoUploader constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(ThemeInterface $theme): void
    {
        if (!$theme->hasLogo()) {
            return;
        }

        if (null !== $theme->getLogoPath() && $this->has($theme->getLogoPath())) {
            $this->remove($theme->getLogoPath());
        }

        do {
            $hash = md5(uniqid((string) mt_rand(), true));
            $filePath = $this->expandPath($hash.'.'.$theme->getLogo()->guessExtension());
        } while ($this->filesystem->has($filePath));

        $theme->setLogoPath($filePath);

        $this->filesystem->write(
            $this->getThemeLogoUploadPath($theme->getLogoPath()),
            file_get_contents($theme->getLogo()->getPathname())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeLogoUploadPath(string $path): string
    {
        return ThemeLogoUploaderInterface::THEME_LOGO_UPLOAD_SUBDIR.DIRECTORY_SEPARATOR.$path;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function expandPath(string $path): string
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }
}
