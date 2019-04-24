<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Uploader;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SeoImageUploader implements SeoImageUploaderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $path;

    public function __construct(Filesystem $filesystem, string $path = 'seo_images')
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
    }

    public function upload(UploadedFile $uploadedFile): string
    {
        do {
            $hash = md5(uniqid((string) mt_rand(), true));
            $filePath = $this->expandPath($hash.'.'.$uploadedFile->guessExtension());
        } while ($this->filesystem->has($filePath));

        $this->filesystem->write(
            $this->getUploadPath($filePath),
            file_get_contents($uploadedFile->getPathname())
        );

        return $filePath;
    }

    public function getUploadPath(string $path): string
    {
        return $this->path.DIRECTORY_SEPARATOR.$path;
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
}
