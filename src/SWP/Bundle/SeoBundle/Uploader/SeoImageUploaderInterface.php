<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface SeoImageUploaderInterface
{
    public function upload(UploadedFile $uploadedFile): string;

    public function getUploadPath(string $path): string;
}
