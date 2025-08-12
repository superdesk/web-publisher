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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class HttpChecker.
 * Generic HTTP service health checker for external services.
 */
class HttpChecker implements ServiceCheckerInterface
{
    private Client $httpClient;
    private string $url;
    private string $name;
    private string $description;
    private bool $critical;
    private int $timeout;
    private array $expectedStatus;

    public function __construct(
        Client $httpClient,
        string $url,
        string $name,
        string $description = '',
        bool $critical = false,
        int $timeout = 5,
        array $expectedStatus = [200, 201, 202, 204]
    ) {
        $this->httpClient = $httpClient;
        $this->url = $url;
        $this->name = $name;
        $this->description = $description ?: sprintf('HTTP service: %s', $name);
        $this->critical = $critical;
        $this->timeout = $timeout;
        $this->expectedStatus = $expectedStatus;
    }

    public function check(): array
    {
        if (empty($this->url)) {
            return [
                'healthy' => true,
                'timestamp' => date('c'),
                'details' => [
                    'status' => 'disabled',
                    'message' => sprintf('%s service URL is not configured', $this->name)
                ]
            ];
        }

        $startTime = microtime(true);
        
        try {
            $response = $this->httpClient->request('GET', $this->url, [
                'timeout' => $this->timeout,
                'connect_timeout' => $this->timeout,
                'http_errors' => false, // Don't throw exceptions for HTTP error status codes
            ]);
            
            $statusCode = $response->getStatusCode();
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $isHealthy = in_array($statusCode, $this->expectedStatus, true);
            
            $details = [
                'response_time_ms' => $responseTime,
                'url' => $this->url,
                'status_code' => $statusCode,
                'expected_status' => $this->expectedStatus,
            ];

            // Try to get response headers
            try {
                $headers = $response->getHeaders();
                if (isset($headers['Server'])) {
                    $details['server'] = $headers['Server'][0] ?? 'unknown';
                }
                if (isset($headers['Content-Type'])) {
                    $details['content_type'] = $headers['Content-Type'][0] ?? 'unknown';
                }
            } catch (\Exception $e) {
                // Headers not critical
            }

            return [
                'healthy' => $isHealthy,
                'timestamp' => date('c'),
                'details' => $details,
                'error' => $isHealthy ? null : sprintf('Unexpected status code: %d', $statusCode)
            ];
            
        } catch (GuzzleException $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    'url' => $this->url,
                    'timeout' => $this->timeout,
                ]
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    'url' => $this->url,
                ]
            ];
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }
}
