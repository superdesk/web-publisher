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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

/**
 * Class ArticleStatistics.
 */
class ArticleStatistics implements ArticleStatisticsInterface, TimestampableInterface
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
    protected $impressionsNumber = 0;

    /**
     * @var int
     */
    protected $pageViewsNumber = 0;

    /**
     * @var float
     */
    protected $internalClickRate = 0;

    /**
     * @var Collection
     */
    protected $events;

    /**
     * ArticleStatistics constructor.
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

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
        if (null === $this->impressionsNumber) {
            return 0;
        }

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
        if (null === $this->pageViewsNumber) {
            return 0;
        }

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
    public function increasePageViewsNumber(): void
    {
        $this->pageViewsNumber = $this->pageViewsNumber + 1;
    }

    public function increaseImpressionsNumber(): void
    {
        $this->impressionsNumber = $this->impressionsNumber + 1;
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

    /**
     * {@inheritdoc}
     */
    public function addEvent(ArticleEventInterface $articleEvent): void
    {
        $this->events->add($articleEvent);
    }

    public function getInternalClickRate(): float
    {
        return $this->internalClickRate;
    }

    public function setInternalClickRate(float $internalClickRate): void
    {
        $this->internalClickRate = $internalClickRate;
    }
}
