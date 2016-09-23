<?php

use FOS\HttpCache\SymfonyCache\PurgeSubscriber;
use FOS\HttpCache\SymfonyCache\RefreshSubscriber;
use FOS\HttpCacheBundle\SymfonyCache\EventDispatchingHttpCache;

class AppCache extends EventDispatchingHttpCache
{
    public function getOptions()
    {
        return array(
            'fos_default_subscribers' => self::SUBSCRIBER_NONE,
        );
    }

    public function getDefaultSubscribers()
    {
        $subscribers = parent::getDefaultSubscribers();
        $subscribers[] = new PurgeSubscriber();
        $subscribers[] = new RefreshSubscriber();

        return $subscribers;
    }
}
