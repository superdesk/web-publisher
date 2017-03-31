<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Model\SettingsInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Serializer\SerializerInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var array
     */
    protected $settingsConfiguration;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EntityRepository
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
     * @param ObjectManager       $em
     * @param SerializerInterface $serializer
     * @param array               $settingsConfiguration
     * @param EntityRepository    $settingsRepository
     */
    public function __construct(
        ObjectManager $em,
        SerializerInterface $serializer,
        array $settingsConfiguration,
        SettingsRepositoryInterface $settingsRepository,
        FactoryInterface $settingsFactory,
        ScopeContextInterface $scopeContext
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->settingsConfiguration = $settingsConfiguration;
        $this->settingsRepository = $settingsRepository;
        $this->settingsFactory = $settingsFactory;
        $this->scopeContext = $scopeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null)
    {
        $this->validateScope($scope);

        $defaultSetting = $this->getFromConfiguration($scope, $name);
        /** @var SettingsInterface $setting */
        $setting = $this->getSettingFromRepository($name, $scope, $owner);

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
        foreach ($this->getSettingsFromRepository() as $setting) {
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
    public function set(string $name, $value, $scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null)
    {
        $this->validateScope($scope);
        $defaultSetting = $this->getFromConfiguration($scope, $name);

        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null === $setting) {
            /** @var SettingsInterface $setting */
            $setting = $this->settingsFactory->create();
            $setting->setName($name);
            $setting->setScope($scope);
            //$setting->setOwner($owner);
            $this->settingsRepository->persist($setting);
        }

        $setting->setValue($this->encodeValue($defaultSetting['type'], $value));
        $this->settingsRepository->flush();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(string $name, $scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null)
    {
        $this->validateScope($scope);

        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null !== $setting) {
            $this->settingsRepository->remove($setting);

            return true;
        }

        return false;
    }

    protected function validateScope($scope)
    {
        if (!in_array($scope, $this->scopeContext->getScopes())) {
            throw new InvalidScopeException($scope);
        }
    }

    private function getFromConfiguration(string $scope = null, $name = null)
    {
        $settings = [];
        if ($name !== null && array_key_exists($name, $this->settingsConfiguration)) {
            $setting = $this->settingsConfiguration[$name];
            if ($setting['scope'] === $scope) {
                return $settings[$name] = $setting;
            }

            throw new InvalidScopeException($scope);
        } elseif ($name !== null) {
            throw new \Exception('There is no setting with this name.');
        }

        foreach ($this->settingsConfiguration as $name => $setting) {
            if ($setting['scope'] === $scope || null === $scope) {
                $setting['value'] = $this->decodeValue($setting['type'], $setting['value']);
                $settings[$name] = $setting;
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
            return $this->serializer->serialize($value, 'json');
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
            return $this->serializer->deserialize($value, 'array', 'json');
        }

        return $value;
    }
}
