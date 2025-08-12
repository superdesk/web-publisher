<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service\HealthCheck\Checker;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class MemcachedChecker.
 */
class MemcachedChecker implements ServiceCheckerInterface
{
    private CacheItemPoolInterface $cache;
    private string $host;
    private int $port;

    public function __construct(CacheItemPoolInterface $cache, string $host = 'localhost', int $port = 11211)
    {
        $this->cache = $cache;
        $this->host = $host;
        $this->port = $port;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Test cache operations
            $testKey = 'health_check_' . uniqid();
            $testValue = 'test_' . time();
            
            // Test write
            $cacheItem = $this->cache->getItem($testKey);
            $cacheItem->set($testValue);
            $cacheItem->expiresAfter(10); // 10 seconds
            $this->cache->save($cacheItem);
            
            // Test read
            $retrievedItem = $this->cache->getItem($testKey);
            if (!$retrievedItem->isHit() || $retrievedItem->get() !== $testValue) {
                throw new \RuntimeException('Cache read/write test failed');
            }
            
            // Clean up test key
            $this->cache->deleteItem($testKey);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $details = [
                'response_time_ms' => $responseTime,
                'host' => $this->host,
                'port' => $this->port,
            ];

            // Try to get additional stats if using Memcached adapter
            if ($this->cache instanceof AdapterInterface) {
                try {
                    // Additional memcached specific checks could be added here
                    $details['adapter'] = get_class($this->cache);
                } catch (\Exception $e) {
                    // Stats not critical
                }
            }

            return [
                'healthy' => true,
                'timestamp' => date('c'),
                'details' => $details
            ];
            
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    'host' => $this->host,
                    'port' => $this->port,
                ]
            ];
        }
    }

    public function getName(): string
    {
        return 'cache';
    }

    public function getDescription(): string
    {
        return 'Memcached Cache Service';
    }

    public function isCritical(): bool
    {
        return true;
    }
}
