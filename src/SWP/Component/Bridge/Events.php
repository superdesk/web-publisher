<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge;

final class Events
{
    /**
     * The SWP_VALIDATION event occurs on an object validation.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const SWP_VALIDATION = 'swp.validation';

    /**
     * The PACKAGE_POST_CREATE event occurs after package is created.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const PACKAGE_POST_CREATE = 'swp.bridge.package_post_create';

    private function __construct()
    {
    }
}
