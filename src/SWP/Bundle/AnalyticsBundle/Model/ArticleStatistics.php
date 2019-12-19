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

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Model\DateTime;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

class ArticleStatistics implements ArticleStatisticsInterface, TimestampableInterface
{
    use TimestampableTrait;

    protected $id;

    protected $article;

    protected $impressionsNumber = 0;

    protected $pageViewsNumber = 0;

    protected $internalClickRate = 0;

    public function __construct()
    {
        $this->createdAt = DateTime::getCurrentDateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
    }

    public function getImpressionsNumber(): int
    {
        if (null === $this->impressionsNumber) {
            return 0;
        }

        return $this->impressionsNumber;
    }

    public function setImpressionsNumber(int $impressionsNumber): void
    {
        $this->impressionsNumber = $impressionsNumber;
    }

    public function getPageViewsNumber(): int
    {
        if (null === $this->pageViewsNumber) {
            return 0;
        }

        return $this->pageViewsNumber;
    }

    public function setPageViewsNumber(int $pageViewsNumber): void
    {
        $this->pageViewsNumber = $pageViewsNumber;
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
