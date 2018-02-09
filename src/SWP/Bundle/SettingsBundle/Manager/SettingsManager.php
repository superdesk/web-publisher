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

use Doctrine\Common\Persistence\ObjectManager;
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

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settingsRepository;

    /**
     * @var FactoryInterface
     */
    protected $settingsFactory;

    /**
     * @var ScopeContextInterface
     */
    protected $scopeContext;

    /**
     * SettingsManager constructor.
     *
     * @param ObjectManager               $em
     * @param SettingsProviderInterface   $settingsProvider
     * @param SettingsRepositoryInterface $settingsRepository
     * @param FactoryInterface            $settingsFactory
     * @param ScopeContextInterface       $scopeContext
     */
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

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null)
    {
        // Allow scope discovery from configuration
        if (null !== $scope) {
            $this->validateScopeAndOwner($scope, $owner);
        }

        $defaultSetting = $this->getFromConfiguration($scope, $name);

        /** @var SettingsInterface $setting */
        $setting = $this->getSettingFromRepository($name, $defaultSetting['scope'], $owner);

        if (null !== $setting) {
            return $this->decodeValue($defaultSetting['type'], $setting->getValue());
        }

        if (null !== $default) {
            return $default;
        }

        return $this->decodeValue($defaultSetting['type'], $defaultSetting['value']);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $settings = $this->getFromConfiguration();

        return $this->processSettings($settings, $this->getSettingsFromRepository());
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByScope(string $scope): array
    {
        $settings = $this->getFromConfiguration($scope);

        $persistedSettings = $this->settingsRepository->findAllByScope($scope)->getQuery()->getResult();

        return $this->processSettings($settings, $persistedSettings);
    }

    private function processSettings(array $settings = [], array $persistedSettings = []): array
    {
        foreach ($persistedSettings as $setting) {
            if (array_key_exists($setting->getName(), $settings)) {
                $settings[$setting->getName()]['value'] = $this->decodeValue(
                    $settings[$setting->getName()]['type'],
                    $setting->getValue()
                );
            }
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null)
    {
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

    /**
     * {@inheritdoc}
     */
    public function clear(string $name, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null)
    {
        $this->validateScopeAndOwner($scope, $owner);

        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null !== $setting) {
            $this->settingsRepository->remove($setting);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAllByScope(string $scope = ScopeContextInterface::SCOPE_GLOBAL): void
    {
        $this->validateScope($scope);

        $this->settingsRepository->removeAllByScope($scope);
    }

    protected function validateScope(string $scope)
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
            throw new \Exception('There is no setting with this name.');
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

    /**
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    private function getSettingFromRepository(string $name, string $scope, SettingsOwnerInterface $owner = null)
    {
        return $this->settingsRepository
            ->findOneByNameAndScopeAndOwner($name, $scope, $owner)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $settingType
     * @param $value
     *
     * @return string
     *
     * @throws \Exception
     */
    private function encodeValue($settingType, $value)
    {
        if (($actualType = gettype($value)) !== $settingType) {
            throw new \Exception(sprintf('Value type should be "%s" not "%s"', $settingType, $actualType));
        }

        if ('array' === $settingType) {
            return json_encode($value);
        }

        return $value;
    }

    /**
     * @param $settingType
     * @param $value
     *
     * @return mixed
     */
    private function decodeValue($settingType, $value)
    {
        if ('array' === $settingType) {
            return json_decode($value, true);
        }

        return $value;
    }
}
