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
use Symfony\Contracts\EventDispatcher\Event;

class ArticleEvent extends Event
{
    protected $article;

    protected $package;

    protected $eventName;

    public function __construct(ArticleInterface $article, PackageInterface $package = null, $eventName = null)
    {
        $this->article = $article;
        $this->package = $package;
        $this->eventName = $eventName;
    }

    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    public function getPackage(): ?PackageInterface
    {
        return $this->package;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }
}
