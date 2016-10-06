<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle;

class RouteEvents
{
    /**
     * The PRE_UPDATE event occurs at the very beginning of route
     * dispatching.
     *
     * This event allows you to modify route before it will be updated.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\RouteEvent")
     *
     * @var string
     */
    const PRE_UPDATE = 'swp.route.pre_update';

    /**
     * The POST_UPDATE event occurs at the very ending of route
     * dispatching.
     *
     * This event allows you to modify route after it will be updated.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\RouteEvent")
     *
     * @var string
     */
    const POST_UPDATE = 'swp.route.post_update';

    /**
     * The PRE_CREATE event occurs before the route is created.
     *
     * This event allows you to modify route before it will be created.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\RouteEvent")
     *
     * @var string
     */
    const PRE_CREATE = 'swp.route.pre_create';

    /**
     * The POST_CREATE event occurs at the very ending of route
     * creation.
     *
     * This event allows you to modify route after it will be created.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\RouteEvent")
     *
     * @var string
     */
    const POST_CREATE = 'swp.route.post_create';
}
