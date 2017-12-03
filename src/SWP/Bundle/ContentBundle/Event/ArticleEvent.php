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

namespace SWP\Bundle\ContentBundle\Event;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use Symfony\Component\EventDispatcher\Event;

class ArticleEvent extends Event
{
    /**
     * @var ArticleInterface
     */
    protected $article;

    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * ArticleEvent constructor.
     *
     * @param ArticleInterface      $article
     * @param PackageInterface|null $package
     * @param string|null           $eventName
     */
    public function __construct(ArticleInterface $article, PackageInterface $package = null, $eventName = null)
    {
        $this->article = $article;
        $this->package = $package;
        $this->eventName = $eventName;
    }

    /**
     * @return ArticleInterface
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return null|string
     */
    public function getEventName()
    {
        return $this->eventName;
    }
}
