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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Class DatabaseChecker.
 */
class DatabaseChecker implements ServiceCheckerInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Test basic connectivity
            $this->connection->connect();
            
            // Test a simple query
            $stmt = $this->connection->prepare('SELECT 1 as test');
            $result = $stmt->executeQuery();
            $row = $result->fetchAssociative();
            
            if ($row['test'] !== 1) {
                throw new \RuntimeException('Database test query returned unexpected result');
            }

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Check connection pool info if available
            $details = [
                'response_time_ms' => $responseTime,
                'driver' => $this->connection->getDriver()->getName(),
                'platform' => $this->connection->getDatabasePlatform()->getName(),
            ];

            // Get database version if possible
            try {
                $versionResult = $this->connection->fetchAssociative('SELECT version() as version');
                $details['version'] = $versionResult['version'] ?? 'unknown';
            } catch (\Exception $e) {
                // Version info not critical
            }

            return [
                'healthy' => true,
                'timestamp' => date('c'),
                'details' => $details
            ];
            
        } catch (Exception $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
                ]
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
                ]
            ];
        }
    }

    public function getName(): string
    {
        return 'database';
    }

    public function getDescription(): string
    {
        return 'PostgreSQL Database Connection';
    }

    public function isCritical(): bool
    {
        return true;
    }
}
