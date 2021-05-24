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
use SWP\Component\TemplatesSystem\Gimme\Event\MetaEvent;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class Context implements \ArrayAccess
{
    const META_EVENT_NAME = 'swp.templates_system.meta.load';

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

    protected $dispatcher;

    protected $metadataCache;

    /**
     * @var string
     */
    protected $configsPath;

    /**
     * @var array
     */
    private $temporaryMeta = [];

    /**
     * @var bool
     */
    private $previewMode = false;

    /**
     * @var array
     */
    private $supportedCache = [];

    /**
     * @var array
     */
    private $configurationCache = [];

    public function __construct(EventDispatcherInterface $dispatcher, Cache $metadataCache, $configsPath = null)
    {
        $this->metadataCache = $metadataCache;
        $this->configsPath = $configsPath;
        $this->dispatcher = $dispatcher;
    }

    public function dispatchMetaEvent(MetaEvent $event): void
    {
        $this->dispatcher->dispatch($event, self::META_EVENT_NAME);
    }

    public function getAvailableConfigs(): array
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
            if (!$this->metadataCache->contains('metadata_config_files')) {
                $finder = new Finder();
                $finder->in($configsPath)->files()->name('*.{yaml,yml}');
                $files = [];
                foreach ($finder as $file) {
                    $files[] = $file->getRealPath();
                    $this->addNewConfig($file->getRealPath());
                }

                $this->metadataCache->save('metadata_config_files', $files);
            } else {
                foreach ($this->metadataCache->fetch('metadata_config_files') as $file) {
                    $this->addNewConfig($file);
                }
            }
        }
    }

    public function getConfigurationForValue($value): array
    {
        if (false === is_object($value)) {
            throw new \Exception('Context supports configuration loading only for objects');
        }

        $objectClassName = get_class($value);
        if (array_key_exists($objectClassName, $this->configurationCache)) {
            return $this->configurationCache[$objectClassName];
        }

        foreach ($this->getAvailableConfigs() as $class => $configuration) {
            if ($value instanceof $class) {
                $this->configurationCache[$objectClassName] = $configuration;

                return $configuration;
            }
        }

        return [];
    }

    public function getMetaForValue($value): Meta
    {
        return new Meta($this, $value, $this->getConfigurationForValue($value));
    }

    public function isSupported($value)
    {
        if (!is_object($value)) {
            return false;
        }

        $objectClassName = get_class($value);
        if (array_key_exists($objectClassName, $this->supportedCache)) {
            return $this->supportedCache[$objectClassName];
        }

        $result = count($this->getConfigurationForValue($value)) > 0 ? true : false;
        $this->supportedCache[$objectClassName] = $result;

        return $result;
    }

    public function addNewConfig(string $filePath)
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
        $this->supportedCache = [];

        return $configuration;
    }

    public function setCurrentPage(Meta $currentPage): self
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

    public function registerMeta(Meta $meta = null)
    {
        $configuration = $meta->getConfiguration();
        $name = $configuration['name'];
        if (!array_key_exists($name, $this->registeredMeta)) {
            $this->registeredMeta[$name] = $configuration;
            if (null !== $meta) {
                $this[$name] = $meta;
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
     * @return bool
     */
    public function isPreviewMode(): bool
    {
        return $this->previewMode;
    }

    /**
     * @param bool $previewMode
     */
    public function setPreviewMode(bool $previewMode)
    {
        $this->previewMode = $previewMode;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $meta)
    {
        if (array_key_exists($name, $this->registeredMeta)) {
            $this->$name = $meta;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return isset($this->$name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        unset($this->$name);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        if (array_key_exists($name, $this->registeredMeta) && isset($this->$name)) {
            return $this->$name;
        }

        return false;
    }

    /**
     * @param array $keys
     *
     * @return string
     */
    public function temporaryUnset(array $keys)
    {
        $metas = [];
        $keysId = md5(serialize($keys));

        if (0 === count($keys)) {
            foreach ($this->registeredMeta as $key => $configuration) {
                if (isset($this[$key])) {
                    $metas[$key] = $this[$key];
                    unset($this[$key]);
                }
            }
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $this->registeredMeta)) {
                $metas[$key] = $this[$key];
                unset($this[$key]);
            }
        }
        $this->temporaryMeta[$keysId] = $metas;

        return $keysId;
    }

    /**
     * @param string $id
     *
     * @return null|true
     */
    public function restoreTemporaryUnset($id)
    {
        $metas = $this->temporaryMeta[$id];
        if (!is_array($metas)) {
            return;
        }

        foreach ($metas as $key => $value) {
            $this[$key] = $value;
        }

        return true;
    }

    /**
     *  Resets context data.
     */
    public function reset()
    {
        $this->currentPage = null;
        $this->registeredMeta = [];
        $this->availableConfigs = [];
        $this->previewMode = false;
    }
}
