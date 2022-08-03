<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\GeoIp;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\GeoIP\Checker\GeoIPChecker;
use SWP\Component\GeoIP\Model\GeoIpPlaceInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CachedGeoIpChecker
{
    public const CACHE_KEY_GEO_IP = '_swp_geoip_';

    private GeoIPChecker $geoIpChecker;

    private CacheInterface $cacheProvider;

    public function __construct(GeoIPChecker $geoIPChecker, CacheInterface $cacheProvider)
    {
        $this->geoIpChecker = $geoIPChecker;
        $this->cacheProvider = $cacheProvider;
    }

    public function isGranted(string $ipAddress, ArticleInterface $article): bool
    {
        if (false === $article instanceof GeoIpPlaceInterface) {
            return false;
        }

        $geoIpPlaces = $article->getGeoIpPlaces();
        $cacheKey = $this->generateCacheKey($ipAddress, $article, $geoIpPlaces);

        return $this->cacheProvider->get($cacheKey, function () use ($ipAddress, $geoIpPlaces) {
            return $this->geoIpChecker->isGranted($ipAddress, $geoIpPlaces);
        });
    }

    private function generateCacheKey(string $ipAddress, ArticleInterface $article, array $geoIpPlaces): string
    {
        $updatedAt = $article->getUpdatedAt();
        if (null !== $updatedAt) {
            $updatedAt = $updatedAt->format(\DateTime::W3C);
        }

        return self::CACHE_KEY_GEO_IP.sha1($updatedAt.$article->getId().$ipAddress.json_encode($geoIpPlaces, JSON_THROW_ON_ERROR, 512));
    }
}
