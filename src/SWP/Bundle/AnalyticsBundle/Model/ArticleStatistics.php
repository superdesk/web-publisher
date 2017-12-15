<?php

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Class ArticleStatistics.
 */
class ArticleStatistics implements ArticleStatisticsInterface, PersistableInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var ArticleInterface
     */
    protected $article;

    /**
     * @var int
     */
    protected $impressionsNumber;

    /**
     * @var int
     */
    protected $pageViewsNumber;

    /**
     * @var Collection
     */
    protected $events;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
    }

    /**
     * {@inheritdoc}
     */
    public function getImpressionsNumber(): int
    {
        return $this->impressionsNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setImpressionsNumber(int $impressionsNumber): void
    {
        $this->impressionsNumber = $impressionsNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewsNumber(): int
    {
        return $this->pageViewsNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setPageViewsNumber(int $pageViewsNumber): void
    {
        $this->pageViewsNumber = $pageViewsNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * {@inheritdoc}
     */
    public function setEvents(Collection $events): void
    {
        $this->events = $events;
    }
}
