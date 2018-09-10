<?php

declare(strict_types=1);

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

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

class ArticleEvent implements ArticleEventInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var null|int
     */
    protected $id;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $pageViewSource;

    /**
     * @var null|RouteInterface
     */
    protected $impressionRoute;

    /**
     * @var null|ArticleInterface
     */
    protected $impressionArticle;

    /**
     * @var null|string
     */
    protected $impressionType;

    /**
     * @var ArticleStatisticsInterface
     */
    protected $articleStatistics;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getPageViewSource(): ?string
    {
        return $this->pageViewSource;
    }

    public function setPageViewSource(string $pageViewSource): void
    {
        $this->pageViewSource = $pageViewSource;
    }

    public function getImpressionRoute(): ?RouteInterface
    {
        return $this->impressionRoute;
    }

    public function setImpressionRoute(?RouteInterface $impressionRoute): void
    {
        $this->impressionRoute = $impressionRoute;
    }

    public function getImpressionArticle(): ?ArticleInterface
    {
        return $this->impressionArticle;
    }

    public function setImpressionArticle(?ArticleInterface $impressionArticle): void
    {
        $this->impressionArticle = $impressionArticle;
    }

    public function getImpressionType(): ?string
    {
        return $this->impressionType;
    }

    public function setImpressionType(?string $impressionType): void
    {
        $this->impressionType = $impressionType;
    }

    public function getArticleStatistics(): ArticleStatisticsInterface
    {
        return $this->articleStatistics;
    }

    public function setArticleStatistics(ArticleStatisticsInterface $articleStatistics): void
    {
        $this->articleStatistics = $articleStatistics;
    }
}
