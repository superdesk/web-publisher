<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SWP\Bundle\AnalyticsBundle\Model\ContentListsAwareTrait;
use SWP\Bundle\ContentBundle\Model\Article as BaseArticle;
use SWP\Component\GeoIP\Model\GeoIpPlaceInterface;
use SWP\Component\GeoIP\Model\Place;
use SWP\Component\MultiTenancy\Model\OrganizationAwareTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Paywall\Model\PaywallSecuredTrait;

class Article extends BaseArticle implements ArticleInterface, GeoIpPlaceInterface
{
    use TenantAwareTrait;
    use OrganizationAwareTrait;
    use PaywallSecuredTrait;
    use ContentListsAwareTrait;

    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @var bool
     */
    protected $isPublishedFBIA = false;

    /**
     * @var ArticleStatisticsInterface
     */
    protected $articleStatistics;

    /**
     * @var ExternalArticleInterface
     */
    protected $externalArticle;

    /**
     * @var int
     */
    protected $commentsCount = 0;

    /** @var Place */
    protected $geoIpPlace;

    /** @var bool */
    protected $isPublishedToAppleNews = false;

    /** @var AppleNewsArticleInterface|null */
    protected $appleNewsArticle;

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackage(): ?PackageInterface
    {
        return $this->package;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackage(?PackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishedFBIA(): bool
    {
        return $this->isPublishedFBIA;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedFBIA(bool $isPublished)
    {
        $this->isPublishedFBIA = $isPublished;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticleStatistics(): ?ArticleStatisticsInterface
    {
        return $this->articleStatistics;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticleStatistics(ArticleStatisticsInterface $articleStatistics): void
    {
        $articleStatistics->setArticle($this);
        $this->articleStatistics = $articleStatistics;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalArticle(): ?ExternalArticleInterface
    {
        return $this->externalArticle;
    }

    /**
     * {@inheritdoc}
     */
    public function setExternalArticle(ExternalArticleInterface $externalArticle): void
    {
        $this->externalArticle = $externalArticle;
    }

    public function getPackageExternalData()
    {
        if (null === $this->getPackage()->getExternalData()) {
            return [];
        }

        $data = [];
        foreach ($this->getPackage()->getExternalData() as $singleData) {
            $data[$singleData->getKey()] = $singleData->getValue();
        }

        return $data;
    }

    public function getCommentsCount(): int
    {
        if (null === $this->commentsCount) {
            return 0;
        }

        return $this->commentsCount;
    }

    public function setCommentsCount(int $commentsCount): void
    {
        $this->commentsCount = $commentsCount;
    }

    public function getGeoIpPlaces(): array
    {
        $places = $this->getPlaces();

        $geoPlaces = [];
        foreach ($places as $place) {
            $geoPlaces[] = new Place($place['country'] ?? '', $place['state'] ?? '');
        }

        return $geoPlaces;
    }

    public function isPublishedToAppleNews(): bool
    {
        return $this->isPublishedToAppleNews;
    }

    public function setPublishedToAppleNews(bool $isPublished): void
    {
        $this->isPublishedToAppleNews = $isPublished;
    }

    public function getAppleNewsArticle(): ?AppleNewsArticleInterface
    {
        return $this->appleNewsArticle;
    }

    public function setAppleNewsArticle(?AppleNewsArticleInterface $appleNewsArticle): void
    {
        $this->appleNewsArticle = $appleNewsArticle;
    }

    public function setPackageUpdatedAt(LifecycleEventArgs $event): void
    {
        $article = $event->getObject();
        if (null === ($updatedAt = $article->getUpdatedAt())) {
            return;
        }

        $entityManager = $event->getEntityManager();
        $this->package->setUpdatedAt($updatedAt);

        $entityManager->flush();
    }
}
