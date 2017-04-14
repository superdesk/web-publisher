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

class ArticleEvents
{
    /**
     * The PRE_CREATE event occurs before the article is created.
     *
     * This event allows you to modify article before it will be created.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const PRE_CREATE = 'swp.article.pre_create';

    /**
     * The POST_CREATE event occurs at the very ending of article
     * creation.
     *
     * This event allows you to modify article after it will be created.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const POST_CREATE = 'swp.article.post_create';

    /**
     * The POST_PUBLISH event occurs at the very ending of article
     * publication.
     *
     * This event allows you to modify article after it will be published.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const POST_PUBLISH = 'swp.article.published';

    /**
     * The PUBLISH event occurs on article publish.
     *
     * This event allows you to modify article during publishing.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const PUBLISH = 'swp.article.publish';

    /**
     * The UNPUBLISH event occurs on article un-publish.
     *
     * This event allows you to modify article during un-publishing.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const UNPUBLISH = 'swp.article.unpublish';

    /**
     * The POST_UNPUBLISH event occurs at the very ending of article
     * unpublishing.
     *
     * This event allows you to modify article after it will be unpublished.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const POST_UNPUBLISH = 'swp.article.unpublished';

    /**
     * The CANCELED event occurs on article cancellation.
     *
     * This event allows you to modify article during cancellation.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ArticleEvent")
     *
     * @var string
     */
    const CANCELED = 'swp.article.canceled';
}
