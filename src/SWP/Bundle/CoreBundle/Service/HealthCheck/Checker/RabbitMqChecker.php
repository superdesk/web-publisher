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

/**
 * Class RabbitMqChecker.
 */
class RabbitMqChecker implements ServiceCheckerInterface
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $vhost;

    public function __construct(
        string $host = '127.0.0.1',
        int $port = 5672,
        string $username = 'guest',
        string $password = 'guest',
        string $vhost = '/'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Test connection by attempting to connect to RabbitMQ management API or socket
            $connectionString = sprintf('tcp://%s:%d', $this->host, $this->port);
            $context = stream_context_create();
            $socket = @stream_socket_client($connectionString, $errno, $errstr, 5);
            
            if (!$socket) {
                throw new \RuntimeException(sprintf('Cannot connect to RabbitMQ: %s', $errstr));
            }
            
            fclose($socket);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $details = [
                'response_time_ms' => $responseTime,
                'host' => $this->host,
                'port' => $this->port,
                'vhost' => $this->vhost,
                'connection_test' => 'socket_connection_successful',
            ];

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
                    'vhost' => $this->vhost,
                ]
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
                    'vhost' => $this->vhost,
                ]
            ];
        }
    }

    public function getName(): string
    {
        return 'rabbitmq';
    }

    public function getDescription(): string
    {
        return 'RabbitMQ Message Queue';
    }

    public function isCritical(): bool
    {
        return false; // Message queue is important but not critical for basic operation
    }
}
