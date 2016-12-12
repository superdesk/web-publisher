<?php

declare(strict_types=1);

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
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Storage\Model\PersistableInterface;

class FacebookInstantArticlesFeed implements TenantAwareInterface, PersistableInterface, FacebookInstantArticlesFeedInterface
{
    use TenantAwareTrait, TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var ContentListInterface
     */
    protected $contentBucket;

    /**
     * @var PageInterface
     */
    protected $facebookPage;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentBucket()
    {
        return $this->contentBucket;
    }

    /**
     * @param ContentListInterface $contentBucket
     */
    public function setContentBucket(ContentListInterface $contentBucket)
    {
        $this->contentBucket = $contentBucket;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacebookPage()
    {
        return $this->facebookPage;
    }

    /**
     * @param PageInterface $facebookPage
     */
    public function setFacebookPage(PageInterface $facebookPage)
    {
        $this->facebookPage = $facebookPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * {@inheritdoc}
     */
    public function isDevelopment()
    {
        if (FacebookInstantArticlesFeedInterface::FEED_MODE_DEVELOPMENT === $this->mode) {
            return true;
        }

        return false;
    }
}
