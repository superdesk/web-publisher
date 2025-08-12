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

use FOS\ElasticaBundle\Elastica\Client;
use Elastica\Exception\ElasticsearchException;

/**
 * Class ElasticsearchChecker.
 */
class ElasticsearchChecker implements ServiceCheckerInterface
{
    private Client $client;
    private string $host;
    private int $port;
    private string $indexName;

    public function __construct(Client $client, string $host = 'localhost', int $port = 9200, string $indexName = 'swp_index')
    {
        $this->client = $client;
        $this->host = $host;
        $this->port = $port;
        $this->indexName = $indexName;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Test cluster health
            $healthResponse = $this->client->request('_cluster/health', 'GET');
            $healthData = $healthResponse->getData();
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $isHealthy = in_array($healthData['status'], ['green', 'yellow'], true);
            
            $details = [
                'response_time_ms' => $responseTime,
                'host' => $this->host,
                'port' => $this->port,
                'cluster_status' => $healthData['status'],
                'cluster_name' => $healthData['cluster_name'] ?? 'unknown',
                'number_of_nodes' => $healthData['number_of_nodes'] ?? 0,
                'number_of_data_nodes' => $healthData['number_of_data_nodes'] ?? 0,
            ];

            // Test index existence and health
            try {
                $indexExists = $this->client->getIndex($this->indexName)->exists();
                $details['index_exists'] = $indexExists;
                $details['index_name'] = $this->indexName;
                
                if ($indexExists) {
                    // Get index stats
                    $indexStats = $this->client->request($this->indexName . '/_stats', 'GET');
                    $statsData = $indexStats->getData();
                    
                    if (isset($statsData['_all']['total']['docs'])) {
                        $details['document_count'] = $statsData['_all']['total']['docs']['count'] ?? 0;
                    }
                }
            } catch (\Exception $e) {
                $details['index_error'] = $e->getMessage();
            }

            // Get version info
            try {
                $versionResponse = $this->client->request('/', 'GET');
                $versionData = $versionResponse->getData();
                $details['version'] = $versionData['version']['number'] ?? 'unknown';
            } catch (\Exception $e) {
                // Version info not critical
            }

            return [
                'healthy' => $isHealthy,
                'timestamp' => date('c'),
                'details' => $details
            ];
            
        } catch (ElasticsearchException $e) {
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
        return 'elasticsearch';
    }

    public function getDescription(): string
    {
        return 'Elasticsearch Search Engine';
    }

    public function isCritical(): bool
    {
        return false; // Search is important but not critical for basic operation
    }
}
