<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Loader;

use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\Yaml\Parser;

abstract class MetaLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $configurationPath;

    /**
     * @var CacheProvider
     */
    protected $metadataCache;

    public function __construct(
        $configurationPath,
        CacheProvider $metadataCache)
    {
        $this->configurationPath = $configurationPath;
        $this->metadataCache = $metadataCache;
    }

    /**
     * Returns the configuration either from the cache or after populating the cache
     *
     * @return false|mixed
     */
    protected function getConfiguration()
    {
        // Cache meta configuration
        $cacheKey = md5($this->configurationPath);
        if (!$this->metadataCache->contains($cacheKey)) {
            if (!is_readable($this->configurationPath)) {
                throw new \InvalidArgumentException('Configuration file is not readable for parser');
            }
            $yaml = new Parser();
            $configuration = $yaml->parse(file_get_contents($this->configurationPath));
            $this->metadataCache->save($cacheKey, $configuration);
        } else {
            $configuration = $this->metadataCache->fetch($cacheKey);
        }

        return $configuration;
    }
}
