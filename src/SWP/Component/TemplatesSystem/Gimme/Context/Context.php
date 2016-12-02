<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
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
     * Array with current page information's.
     *
     * @var Meta
     */
    protected $currentPage;

    /**
     * Array will all registered meta types.
     *
     * @var Meta[]
     */
    protected $registeredMeta = [];

    /**
     * Array with available meta configs.
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
     * @param Cache  $metadataCache
     * @param string $configsPath
     */
    public function __construct(Cache $metadataCache, $configsPath = null)
    {
        $this->metadataCache = $metadataCache;
        $this->configsPath = $configsPath;
    }

    /**
     * @return array
     */
    public function getAvailableConfigs()
    {
        if (0 === count($this->availableConfigs)) {
            $this->loadConfigsFromPath($this->configsPath);
        }

        return $this->availableConfigs;
    }

    /**
     * @param array $configuration
     *
     * @return bool
     */
    public function addAvailableConfig(array $configuration)
    {
        if (isset($configuration['class']) && !isset($this->availableConfigs[$configuration['class']])) {
            $this->availableConfigs[$configuration['class']] = $configuration;

            return true;
        }

        return false;
    }

    /**
     * @param array $availableConfigs
     *
     * @return Context
     */
    public function setAvailableConfigs(array $availableConfigs)
    {
        $this->availableConfigs = $availableConfigs;

        return $this;
    }

    /**
     * @param string $configsPath
     */
    public function loadConfigsFromPath($configsPath)
    {
        if (file_exists($configsPath)) {
            $finder = new Finder();
            $finder->in($configsPath)->files()->name('*.yml');

            foreach ($finder as $file) {
                $this->addNewConfig($file->getRealPath());
            }
        }
    }

    /**
     * @param mixed $value
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getConfigurationForValue($value)
    {
        if (false === is_object($value)) {
            throw new \Exception('Context supports configuration loading only for objects');
        }

        foreach ($this->getAvailableConfigs() as $class => $configuration) {
            if ($value instanceof $class) {
                return $configuration;
            }
        }

        return [];
    }

    /**
     * @param mixed $value
     *
     * @return Meta
     */
    public function getMetaForValue($value)
    {
        return new Meta($this, $value, $this->getConfigurationForValue($value));
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isSupported($value)
    {
        if (!is_object($value)) {
            return false;
        }

        return count($this->getConfigurationForValue($value)) > 0 ? true : false;
    }

    /**
     * @param string $filePath
     *
     * @return $this
     */
    public function addNewConfig($filePath)
    {
        $cacheKey = md5($filePath);
        if (!$this->metadataCache->contains($cacheKey)) {
            if (!is_readable($filePath)) {
                throw new \InvalidArgumentException('Configuration file is not readable for parser');
            }
            $parser = new Parser();
            $configuration = $parser->parse(file_get_contents($filePath));
            $this->metadataCache->save($cacheKey, $configuration);
        } else {
            $configuration = $this->metadataCache->fetch($cacheKey);
        }

        $this->addAvailableConfig($configuration);

        return $configuration;
    }

    /**
     * Set current context page information's.
     *
     * @param Meta $currentPage
     *
     * @return self
     */
    public function setCurrentPage(Meta $currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get current context page information's.
     *
     * @return Meta
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Register new meta type, registration is required before setting new value for meta.
     *
     * @param Meta|null $meta Meta object
     *
     * @throws \Exception if already registered
     *
     * @return bool if registered successfully
     */
    public function registerMeta(Meta $meta = null)
    {
        $configuration = $meta->getConfiguration();
        $name = $configuration['name'];
        if (!array_key_exists($name, $this->registeredMeta)) {
            $this->registeredMeta[$name] = $configuration;

            if (!is_null($meta)) {
                $this->$name = $meta;
            }

            return true;
        }

        return false;
    }

    /**
     * @return Meta[]
     */
    public function getRegisteredMeta()
    {
        return $this->registeredMeta;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $meta)
    {
        if (in_array($name, $this->registeredMeta)) {
            $this->$name = $meta;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return in_array($name, $this->registeredMeta);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        unset($this->registeredMeta[$name]);
        usent($this->$name);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        if (in_array($name, $this->registeredMeta)) {
            return $this->$name;
        }

        return false;
    }
}
