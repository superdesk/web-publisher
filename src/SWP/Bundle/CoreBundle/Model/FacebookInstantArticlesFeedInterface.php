<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use SWP\Component\Common\Model\TimestampableInterface;

interface FacebookInstantArticlesFeedInterface extends TimestampableInterface
{
    /**
     * Use Instant Articles production environment.
     */
    const FEED_MODE_PRODUCTION = 1;

    /**
     * Use Instant Articles development sandbox.
     */
    const FEED_MODE_DEVELOPMENT = 0;

    /**
     * @return ContentListInterface
     */
    public function getContentBucket();

    /**
     * @return PageInterface
     */
    public function getFacebookPage();

    /**
     * @return bool
     */
    public function isDevelopment();

    /**
     * @return int
     */
    public function getMode();

    /**
     * @param $mode
     */
    public function setMode($mode);
}
