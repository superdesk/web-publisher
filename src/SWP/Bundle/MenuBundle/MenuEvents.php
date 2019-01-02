<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MenuBundle;

final class MenuEvents
{
    /**
     * The MENU_CREATED event occurs after menu is created.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MENU_CREATED = 'swp.menu.created';

    /**
     * The MENU_DELETED event occurs after menu is deleted.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MENU_DELETED = 'swp.menu.deleted';

    /**
     * The MENU_UPDATED event occurs after menu is updated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MENU_UPDATED = 'swp.menu.updated';

    private function __construct()
    {
    }
}
