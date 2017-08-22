<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Uploader;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class TenantAwareThemeUploader.
 */
final class TenantAwareThemeUploader implements ThemeUploaderInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var string
     */
    private $baseDir;

    /**
     * TenantAwareThemeUploader constructor.
     *
     * @param TenantContextInterface $tenantContext
     * @param string                 $baseDir
     */
    public function __construct(TenantContextInterface $tenantContext, string $baseDir)
    {
        $this->tenantContext = $tenantContext;
        $this->baseDir = $baseDir;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFile $file)
    {
        if (null === $this->tenantContext->getTenant()) {
            throw new \Exception('Tenant was not found in context!');
        }
        $destinationFolder = $this->getAvailableThemesPath();
        $filePath = $file->getRealPath();
        $filesystem = new Filesystem();

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            if (!$filesystem->exists($destinationFolder)) {
                $filesystem->mkdir($destinationFolder);
            }
            $pathInArray = explode('/', $zip->getNameIndex(0));
            $themeDirInZip = array_shift($pathInArray);

            if (false === $zip->getFromName($themeDirInZip.DIRECTORY_SEPARATOR.'theme.json')) {
                throw new \Exception('In ZIP file we expect one directory and theme.json file inside');
            }

            if ($filesystem->exists($destinationFolder.DIRECTORY_SEPARATOR.$themeDirInZip)) {
                $filesystem->remove($destinationFolder.DIRECTORY_SEPARATOR.$themeDirInZip);
            }

            $zip->extractTo($destinationFolder);
            $zip->close();

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getAvailableThemesPath()
    {
        $organizationCode = $this->tenantContext->getTenant()->getOrganization()->getCode();

        return sprintf($this->baseDir.DIRECTORY_SEPARATOR.ThemeUploaderInterface::AVAILABLE_THEMES_PATH, $organizationCode);
    }
}
