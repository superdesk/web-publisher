<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Manager;

use Doctrine\Persistence\ObjectManager;
use Exception;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Exception\InvalidOwnerException;
use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Model\SettingsInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsRepositoryInterface;
use SWP\Bundle\SettingsBundle\Provider\SettingsProviderInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class SettingsManager implements SettingsManagerInterface
{
    protected $settingsProvider;

    protected $em;

    protected $settingsRepository;

    protected $settingsFactory;

    protected $scopeContext;

    protected $internalCache = [];

    public function __construct(
        ObjectManager $em,
        SettingsProviderInterface $settingsProvider,
        SettingsRepositoryInterface $settingsRepository,
        FactoryInterface $settingsFactory,
        ScopeContextInterface $scopeContext
    ) {
        $this->em = $em;
        $this->settingsProvider = $settingsProvider;
        $this->settingsRepository = $settingsRepository;
        $this->settingsFactory = $settingsFactory;
        $this->scopeContext = $scopeContext;
    }

    public function get(string $name, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null)
    {
        $keyElements = [$name, $scope, $default, $this->scopeContext->getScopes()];
        if (null !== $owner) {
            $keyElements[] = $owner->getId();
        }
        $cacheKey = md5(json_encode($keyElements, JSON_THROW_ON_ERROR, 512));
        if (isset($this->internalCache[$cacheKey])) {
            return $this->internalCache[$cacheKey];
        }

        // Allow scope discovery from configuration
        if (null !== $scope) {
            $this->validateScopeAndOwner($scope, $owner);
        }

        $defaultSetting = $this->getFromConfiguration($scope, $name);

        /** @var SettingsInterface $setting */
        $setting = $this->getSettingFromRepository($name, $defaultSetting['scope'], $owner);

        if (null !== $setting) {
            $value = $this->decodeValue($defaultSetting['type'], $setting->getValue());
            $this->internalCache[$cacheKey] = $value;

            return $value;
        }

        if (null !== $default) {
            $this->internalCache[$cacheKey] = $default;

            return $default;
        }

        $value = $this->decodeValue($defaultSetting['type'], $defaultSetting['value']);
        $this->internalCache[$cacheKey] = $value;

        return $value;
    }

    public function all(): array
    {
        $cacheKey = md5(json_encode(['all', $this->scopeContext], JSON_THROW_ON_ERROR, 512));
        if (isset($this->internalCache[$cacheKey])) {
            return $this->internalCache[$cacheKey];
        }

        $settings = $this->getFromConfiguration();
        $settings = $this->processSettings($settings, $this->getSettingsFromRepository());
        $this->internalCache[$cacheKey] = $settings;

        return $settings;
    }

    public function getByScopeAndOwner(string $scope, SettingsOwnerInterface $settingsOwner): array
    {
        $settings = $this->getFromConfiguration($scope);
        $persistedSettings = $this->settingsRepository->findByScopeAndOwner($scope, $settingsOwner)->getQuery()->getResult();

        return $this->processSettings($settings, $persistedSettings);
    }

    public function set(string $name, $value, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null)
    {
        $this->internalCache = [];
        $this->validateScopeAndOwner($scope, $owner);
        $defaultSetting = $this->getFromConfiguration($scope, $name);

        /** @var SettingsInterface $setting */
        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null === $setting) {
            /** @var SettingsInterface $setting */
            $setting = $this->settingsFactory->create();
            $setting->setName($name);
            $setting->setScope($scope);

            if (null !== $owner) {
                $setting->setOwner($owner->getId());
            }
            $this->settingsRepository->persist($setting);
        } else {
            $setting->setUpdatedAt(new \DateTime());
        }

        $setting->setValue($this->encodeValue($defaultSetting['type'], $value));
        $this->settingsRepository->flush();

        return $setting;
    }

    public function getOneSettingByName(string $name): ?array
    {
        foreach ($this->all() as $setting) {
            if ($setting['name'] === $name) {
                return $setting;
            }
        }

        return null;
    }

    public function clear(string $name, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null): bool
    {
        $this->internalCache = [];
        $this->validateScopeAndOwner($scope, $owner);

        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null !== $setting) {
            $this->settingsRepository->remove($setting);

            return true;
        }

        return false;
    }

    public function clearAllByScope(string $scope = ScopeContextInterface::SCOPE_GLOBAL): void
    {
        $this->validateScope($scope);

        $this->settingsRepository->removeAllByScope($scope);
    }

    protected function validateScope(string $scope): void
    {
        if (!\in_array($scope, $this->scopeContext->getScopes(), true)) {
            throw new InvalidScopeException($scope);
        }
    }

    protected function validateScopeAndOwner(string $scope, $owner = null)
    {
        $this->validateScope($scope);

        if (ScopeContextInterface::SCOPE_GLOBAL !== $scope && null === $owner) {
            throw new InvalidOwnerException($scope);
        }
    }

    private function processSettings(array $settings = [], array $persistedSettings = []): array
    {
        $convertedSettings = [];

        foreach ($settings as $key => $setting) {
            $setting['name'] = $key;
            $convertedSettings[] = $setting;
        }

        foreach ($persistedSettings as $key => $setting) {
            foreach ($convertedSettings as $keyConverted => $convertedSetting) {
                if (isset($convertedSetting['name']) && $convertedSetting['name'] === $setting->getName()) {
                    $convertedSetting['value'] = $this->decodeValue(
                        $convertedSetting['type'],
                        $setting->getValue()
                    );

                    $convertedSettings[$keyConverted] = $convertedSetting;
                }
            }
        }

        return $convertedSettings;
    }

    private function getFromConfiguration(string $scope = null, $name = null)
    {
        $settings = [];
        $settingsConfig = $this->settingsProvider->getSettings();

        if (null !== $name && array_key_exists($name, $settingsConfig)) {
            $setting = $settingsConfig[$name];
            if (null === $scope || $setting['scope'] === $scope) {
                return $settings[$name] = $setting;
            }

            throw new InvalidScopeException($scope);
        }

        if (null !== $name) {
            throw new Exception('There is no setting with this name.');
        }

        foreach ($settingsConfig as $key => $setting) {
            if (null === $scope || $setting['scope'] === $scope) {
                $setting['value'] = $this->decodeValue($setting['type'], $setting['value']);
                $settings[$key] = $setting;
            }
        }

        return $settings;
    }

    /**
     * @return array|mixed
     */
    private function getSettingsFromRepository()
    {
        return $this->settingsRepository->findAllByScopeAndOwner($this->scopeContext)->getQuery()->getResult();
    }

    private function getSettingFromRepository(string $name, string $scope, SettingsOwnerInterface $owner = null): ?SettingsInterface
    {
        return $this->settingsRepository
            ->findOneByNameAndScopeAndOwner($name, $scope, $owner)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function encodeValue(string $settingType, $value)
    {
        if ('string' === $settingType) {
            return (string) $value;
        }

        if (($actualType = gettype($value)) !== $settingType) {
            throw new Exception(sprintf('Value type should be "%s" not "%s"', $settingType, $actualType));
        }

        if ('array' === $settingType) {
            return json_encode($value);
        }

        return $value;
    }

    private function decodeValue(string $settingType, $value)
    {
        if ('array' === $settingType) {
            return json_decode($value, true);
        }

        if ('boolean' === $settingType) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ('integer' === $settingType) {
            return filter_var($value, FILTER_VALIDATE_INT);
        }

        return $value;
    }
}
