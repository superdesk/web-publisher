<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Gimme\Context;

use Doctrine\Common\Cache\Cache;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class Context implements \ArrayAccess
{
    /**
     * Array with current page informations.
     *
     * @var string[]
     */
    protected $currentPage;

    /**
     * Array will all registered meta types.
     *
     * @var \SWP\TemplatesSystem\Gimme\Meta\Meta[]
     */
    protected $registeredMeta = [];

    /**
     * Array with available meta configs
     *
     * @var array
     */
    protected $availableConfigs = [];

    /**
     * @var Cache
     */
    protected $metadataCache;

    /**
     * @var string
     */
    protected $configsPath;

    /**
     * Context constructor.
     *
     * @param Cache $metadataCache
     */
    public function __construct(Cache $metadataCache, $configsPath)
    {
        $this->metadataCache = $metadataCache;
        $this->configsPath = $configsPath;

        $finder = new Finder();
        $finder->in($configsPath)->files()->name('*.yml');

        foreach ($finder as $file) {
            $this->addNewConfig($file->getRealPath());
        }
    }

    /**
     * @return array
     */
    public function getAvailableConfigs()
    {
        return $this->availableConfigs;
    }

    /**
     * @param array $availableConfigs
     *
     * @return Context
     */
    public function setAvailableConfigs($availableConfigs)
    {
        $this->availableConfigs = $availableConfigs;

        return $this;
    }

    public function getConfigurationForValue($value)
    {
        if (false === is_object($value)) {
            throw new \Exception('Context supports configuration loading only for objects');
        }

        foreach ($this->availableConfigs as $class => $configuration) {
            if ($value instanceof $class) {
                return $configuration;
            }
        }

        return;
    }

    public function getMetaForValue($value)
    {
        return new Meta($this, $value, $this->getConfigurationForValue($value));
    }

    public function isSupported($value)
    {
        if (!is_object($value)) {
            return false;
        }

        return null !== $this->getConfigurationForValue($value);
    }

    /**
     * @param string $filePath
     *
     * @return $this
     */
    public function addNewConfig($filePath)
    {
        // Cache meta configuration
        $cacheKey = md5($filePath);
        if (!$this->metadataCache->contains($cacheKey)) {
            if (!is_readable($filePath)) {
                throw new \InvalidArgumentException('Configuration file is not readable for parser');
            }
            $yaml = new Parser();
            $configuration = $yaml->parse(file_get_contents($filePath));
            $this->metadataCache->save($cacheKey, $configuration);
        } else {
            $configuration = $this->metadataCache->fetch($cacheKey);
        }
        if (true !== isset($this->availableConfigs[$configuration['class']])) {
            $this->availableConfigs[$configuration['class']] = $configuration;
        }

        return $this;
    }

    /**
     * Set current context page informations.
     *
     * @param string $currentPage
     *
     * @return self
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get current context page informations.
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Register new meta type, registration is required before setting new value for meta.
     *
     * @param string                                   $name Name of meta
     * @param SWP\TemplatesSystem\Gimme\Meta\Meta|null $meta Meta object
     *
     * @throws \Exception if already registered
     *
     * @return bool if registered successfully
     */
    public function registerMeta($name, Meta $meta = null)
    {
        if (!in_array($name, $this->registeredMeta)) {
            $this->registeredMeta[] = $name;

            if (!is_null($meta)) {
                $this->$name = $meta;
            }

            return true;
        }

        throw new \Exception(sprintf('Meta with name %s is already registered', $name));
    }

    public function getRegisteredMeta()
    {
        return $this->registeredMeta;
    }

    public function offsetSet($name, $meta)
    {
        if (in_array($name, $this->registeredMeta)) {
            $this->$name = $meta;
        }

        return true;
    }

    public function offsetExists($name)
    {
        return in_array($name, $this->registeredMeta);
    }

    public function offsetUnset($name)
    {
        unset($this->registeredMeta[$name]);
        usent($this->$name);

        return true;
    }

    public function offsetGet($name)
    {
        if (in_array($name, $this->registeredMeta)) {
            return $this->$name;
        }

        return false;
    }
}
