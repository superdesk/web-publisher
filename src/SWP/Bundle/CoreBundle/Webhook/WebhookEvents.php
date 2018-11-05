<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Webhook;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\RouteEvents;
use SWP\Bundle\MenuBundle\MenuEvents;
use SWP\Component\Bridge\Events as PackageEvents;

/**
 * Interface WebhookEvents.
 */
interface WebhookEvents
{
    const EVENTS = [
        'article[updated]' => ArticleEvents::POST_UPDATE,
        'article[created]' => ArticleEvents::POST_CREATE,
        'article[published]' => ArticleEvents::POST_PUBLISH,
        'article[unpublished]' => ArticleEvents::POST_UNPUBLISH,
        'article[canceled]' => ArticleEvents::CANCELED,
        'menu[created]' => MenuEvents::MENU_CREATED,
        'menu[updated]' => MenuEvents::MENU_UPDATED,
        'menu[deleted]' => MenuEvents::MENU_DELETED,
        'route[created]' => RouteEvents::POST_CREATE,
        'route[updated]' => RouteEvents::POST_UPDATE,
        'route[deleted]' => RouteEvents::POST_DELETE,
        'package[created]' => PackageEvents::PACKAGE_POST_CREATE,
        'package[updated]' => PackageEvents::PACKAGE_POST_UPDATE,
        'package[processed]' => PackageEvents::PACKAGE_PROCESSED,
    ];
}
