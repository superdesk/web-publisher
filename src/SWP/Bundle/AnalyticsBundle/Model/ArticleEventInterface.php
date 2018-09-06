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
use SWP\Component\Storage\Model\PersistableInterface;

interface ArticleEventInterface extends PersistableInterface
{
    const ACTION_IMPRESSION = 'impression';

    const ACTION_PAGEVIEW = 'pageview';

    const ACTION_LINK_CLICKED = 'linkclicked';

    const ACTION_SCROLL_DEPTH = 'scrolldepth';

    const IMPRESSION_TYPE_HOMEPAGE = 'homepage';

    const IMPRESSION_TYPE_COLLECTION = 'collection';

    const IMPRESSION_TYPE_ARTICLE = 'article';

    public function getAction(): string;

    public function setAction(string $action): void;

    public function getImpressionRoute(): ?RouteInterface;

    public function setImpressionRoute(?RouteInterface $impressionRoute): void;

    public function getImpressionArticle(): ?ArticleInterface;

    public function setImpressionArticle(?ArticleInterface $impressionArticle): void;

    public function getImpressionType(): ?string;

    public function setImpressionType(?string $impressionType): void;

    public function getArticleStatistics(): ArticleStatisticsInterface;

    public function setArticleStatistics(ArticleStatisticsInterface $articleStatistics): void;
}
