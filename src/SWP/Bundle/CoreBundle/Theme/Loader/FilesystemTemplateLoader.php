<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Loader;

use Exception;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig\Loader\ExistsLoaderInterface;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class FilesystemTemplateLoader implements LoaderInterface, ExistsLoaderInterface
{
    protected $decoratedLoader;

    protected $templateLocator;

    protected $templateNameParser;

    /** @var string[] */
    protected $cache = [];

    protected $themeAssetProvider;

    public function __construct(
        LoaderInterface $decoratedLoader,
        FileLocatorInterface $templateLocator,
        TemplateNameParserInterface $templateNameParser,
        ThemeAssetProviderInterface $themeAssetProvider
    ) {
        $this->decoratedLoader = $decoratedLoader;
        $this->templateLocator = $templateLocator;
        $this->templateNameParser = $templateNameParser;
        $this->themeAssetProvider = $themeAssetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name): Source
    {
        try {
            $path = $this->findTemplate($name);

            return new Source($this->themeAssetProvider->readFile($path), (string) $name, $path);
        } catch (Exception $exception) {
            return $this->decoratedLoader->getSourceContext($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name): string
    {
        try {
            return $this->findTemplate($name);
        } catch (Exception $exception) {
            return $this->decoratedLoader->getCacheKey($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time): bool
    {
        try {
            return $this->themeAssetProvider->getTimestamp($this->findTemplate($name)) <= $time;
        } catch (Exception $exception) {
            return $this->decoratedLoader->isFresh($name, $time);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name): bool
    {
        try {
            if (!$this->themeAssetProvider->hasFile($this->findTemplate($name))) {
                return $this->decoratedLoader->exists($name);
            }
        } catch (Exception $e) {
            return $this->decoratedLoader->exists($name);
        }

        return true;
    }

    protected function findTemplate($logicalName): string
    {
        $logicalName = (string) $logicalName;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $template = $this->templateNameParser->parse($logicalName);

        /** @var string $file */
        $file = $this->templateLocator->locate($template);

        return $this->cache[$logicalName] = $file;
    }
}
