<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Translation;

use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Symfony\Component\Finder\SplFileInfo;

final class S3TranslationFilesFinder implements TranslationFilesFinderInterface
{
    private $themeAssetProvider;

    public function __construct(ThemeAssetProviderInterface $themeAssetProvider)
    {
        $this->themeAssetProvider = $themeAssetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function findTranslationFiles(string $path): array
    {
        $themeFiles = $this->getFiles($path);

        $translationsFiles = [];
        foreach ($themeFiles as $themeFile) {
            $themeFilepath = (string) $themeFile;

            if (!$this->isTranslationFile($themeFilepath)) {
                continue;
            }

            $translationsFiles[] = $themeFilepath;
        }

        return $translationsFiles;
    }

    /**
     * @return iterable|SplFileInfo[]
     */
    private function getFiles(string $path): iterable
    {
        $files = $this->themeAssetProvider->listContents($path.'/translations', true);
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file['path'];
        }

        return $paths;
    }

    private function isTranslationFile(string $file): bool
    {
        return false !== strpos($file, 'translations'.\DIRECTORY_SEPARATOR)
            && (bool) preg_match('/^[^\.]+?\.[a-zA-Z_]{2,}?\.[a-z0-9]{2,}?$/', basename($file));
    }
}
