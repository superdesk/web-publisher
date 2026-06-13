<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Migrations;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Replacement for Symfony's removed ContainerAwareTrait, used by data
 * migrations together with {@see ContainerAwareInterface}.
 */
trait ContainerAwareTrait
{
    protected ?ContainerInterface $container = null;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }
}
