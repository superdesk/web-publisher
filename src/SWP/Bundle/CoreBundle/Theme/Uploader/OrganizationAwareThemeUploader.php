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
 * Class OrganizationAwareThemeUploader.
 */
final class OrganizationAwareThemeUploader implements ThemeUploaderInterface {
  /**
   * @var TenantContextInterface
   */
  private $tenantContext;

  /**
   * @var string
   */
  private $baseDir;

  /**
   * OrganizationAwareThemeUploader constructor.
   *
   * @param TenantContextInterface $tenantContext
   * @param string $baseDir
   */
  public function __construct(TenantContextInterface $tenantContext, string $baseDir) {
    $this->tenantContext = $tenantContext;
    $this->baseDir = $baseDir;
  }

  /**
   * {@inheritdoc}
   */
  public function upload(UploadedFile $file) {
    if (null === $this->tenantContext->getTenant()) {
      throw new \Exception('Tenant was not found in context!');
    }

    $destinationFolder = $this->getAvailableThemesPath();
    $filePath = $file->getRealPath();
    $filesystem = new Filesystem();

    $zip = new \ZipArchive();
    if (true === $zip->open($filePath)) {
      if (!$filesystem->exists($destinationFolder)) {
        $filesystem->mkdir($destinationFolder);
      }
      $pathInArray = explode('/', $zip->getNameIndex(0));
      $themeDirInZip = array_shift($pathInArray);

      $themeConfiguration = $zip->getFromName($themeDirInZip . DIRECTORY_SEPARATOR . 'theme.json');
      if (false === $themeConfiguration) {
        throw new \Exception('In ZIP file we expect one directory and theme.json file inside');
      }

      $themeConfiguration = \json_decode($themeConfiguration, true);
      if (\JSON_ERROR_NONE !== json_last_error()) {
        throw new \Exception('Theme configuration is not valid. Syntax error in theme.json');
      }

      $themeName = $themeConfiguration['name'];
      $unpackedThemePath = $destinationFolder . DIRECTORY_SEPARATOR . $themeDirInZip;

      if ($filesystem->exists($unpackedThemePath)) {
        $filesystem->remove($unpackedThemePath);
      }

      $zip->extractTo($destinationFolder);
      $zip->close();

      $finalPath = $destinationFolder . DIRECTORY_SEPARATOR . str_replace('/', '__', $themeName);
      $filesystem->rename($unpackedThemePath, $finalPath, true);

      return $finalPath;
    }

    return false;
  }

  /**
   * @return string
   */
  public function getAvailableThemesPath() {
    $tenant = $this->tenantContext->getTenant();
    $organization = $tenant->getOrganization();
    $organizationCode = $organization->getCode();
    return sprintf($this->baseDir . DIRECTORY_SEPARATOR . ThemeUploaderInterface::AVAILABLE_THEMES_PATH, $organizationCode);
  }
}
