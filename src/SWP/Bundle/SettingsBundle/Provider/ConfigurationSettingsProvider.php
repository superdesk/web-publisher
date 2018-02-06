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

final class ConfigurationSettingsProvider implements SettingsProviderInterface
{
    private $settingsConfiguration = [];

    public function __construct(array $settingsConfiguration = [])
    {
        $this->settingsConfiguration = $settingsConfiguration;
    }

    public function getSettings(): array
    {
        return $this->settingsConfiguration;
    }

    public function supports(): bool
    {
        return true;
    }
}
