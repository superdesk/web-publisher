<?php

use FOS\HttpCache\SymfonyCache\PurgeSubscriber;
use FOS\HttpCache\SymfonyCache\RefreshSubscriber;
use FOS\HttpCacheBundle\SymfonyCache\EventDispatchingHttpCache;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class AppCache extends EventDispatchingHttpCache
{
    /**
     * Overwrite constructor to register event subscribers for FOSHttpCache.
     */
    public function __construct(HttpKernelInterface $kernel, $cacheDir = null)
    {
        parent::__construct($kernel, $cacheDir);

        $this->addSubscriber(new PurgeSubscriber());
        $this->addSubscriber(new RefreshSubscriber());
    }
}
