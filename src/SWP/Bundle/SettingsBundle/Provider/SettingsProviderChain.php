<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Provider;

final class SettingsProviderChain implements SettingsProviderInterface
{
    /**
     * @var array
     */
    private $providers = [];

    /**
     * SettingsProviderChain constructor.
     *
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        $settings = [];
        /** @var SettingsProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($provider->supports()) {
                $settings += $provider->getSettings();
            }
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports()) {
                return true;
            }
        }

        return false;
    }
}
