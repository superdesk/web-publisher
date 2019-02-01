<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Matcher;

use Doctrine\Common\Cache\Cache;
use Psr\Log\LoggerInterface;
use Symfony\Cmf\Component\Routing\ChainRouter;

class CachedUrlMatcher extends ChainRouter
{
    /**
     * @var Cache
     */
    protected $cacheProvider;

    public function __construct(Cache $cacheProvider, LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->cacheProvider = $cacheProvider;
    }

    public function match($pathinfo)
    {
        $cacheKey = $this->getCacheKey($pathinfo);
        if ($this->cacheProvider->contains($cacheKey)) {
            return $this->cacheProvider->fetch($cacheKey);
        }

        $result = parent::match($pathinfo);
        $this->cacheProvider->save($cacheKey, $result);

        return $result;
    }

    private function getCacheKey(string $pathinfo)
    {
        return sprintf('pathinfo__%s', md5($pathinfo));
    }
}
