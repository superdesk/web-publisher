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

namespace SWP\Bundle\CoreBundle\Service\HealthCheck;

use SWP\Bundle\CoreBundle\Service\HealthCheck\Checker\ServiceCheckerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HealthCheckService.
 */
class HealthCheckService implements HealthCheckServiceInterface
{
    private array $checkers = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addChecker(string $name, ServiceCheckerInterface $checker): void
    {
        $this->checkers[$name] = $checker;
    }

    public function checkBasic(): array
    {
        return [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'application' => 'Superdesk Web Publisher',
            'version' => $this->getApplicationVersion()
        ];
    }

    public function checkAll(bool $detailed = false): array
    {
        $results = [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'application' => 'Superdesk Web Publisher',
            'version' => $this->getApplicationVersion(),
            'services' => []
        ];

        $overallHealthy = true;
        $criticalServices = ['database', 'cache', 'supervisor'];

        foreach ($this->checkers as $name => $checker) {
            try {
                $serviceResult = $checker->check();
                $results['services'][$name] = $serviceResult;

                if (!$serviceResult['healthy']) {
                    if (in_array($name, $criticalServices, true)) {
                        $overallHealthy = false;
                    }
                    
                    if ($detailed) {
                        $this->logger->warning(sprintf('Health check failed for service: %s', $name), [
                            'service' => $name,
                            'error' => $serviceResult['error'] ?? 'Unknown error'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $results['services'][$name] = [
                    'healthy' => false,
                    'error' => $e->getMessage(),
                    'timestamp' => date('c')
                ];

                if (in_array($name, $criticalServices, true)) {
                    $overallHealthy = false;
                }

                $this->logger->error(sprintf('Health check exception for service: %s', $name), [
                    'service' => $name,
                    'exception' => $e->getMessage()
                ]);
            }
        }

        $results['status'] = $overallHealthy ? 'healthy' : 'unhealthy';

        return $results;
    }

    public function checkAllSimplified(): array
    {
        $results = [
            'application_name' => 'Publish'
        ];

        $overallHealthy = true;
        $criticalServices = ['database', 'cache', 'supervisor'];
        
        // Service name mapping for simplified output
        $serviceMapping = [
            'database' => 'postgresql',
            'cache' => 'memcached',
            'elasticsearch' => 'elastic',
            'rabbitmq' => 'rabbitmq',
            'mailer' => 'mailer',
            'supervisor' => 'supervisor'
        ];

        foreach ($this->checkers as $name => $checker) {
            $displayName = $serviceMapping[$name] ?? $name;
            
            try {
                $serviceResult = $checker->check();
                
                // Convert boolean healthy status to color
                if ($serviceResult['healthy']) {
                    $results[$displayName] = 'green';
                } else {
                    // Check if it's a critical service
                    if (in_array($name, $criticalServices, true)) {
                        $results[$displayName] = 'red';
                        $overallHealthy = false;
                    } else {
                        // Non-critical services show yellow when unhealthy
                        $results[$displayName] = 'yellow';
                    }
                }
            } catch (\Exception $e) {
                // Exception means service is down
                if (in_array($name, $criticalServices, true)) {
                    $results[$displayName] = 'red';
                    $overallHealthy = false;
                } else {
                    $results[$displayName] = 'yellow';
                }

                $this->logger->error(sprintf('Health check exception for service: %s', $name), [
                    'service' => $name,
                    'exception' => $e->getMessage()
                ]);
            }
        }

        // Set overall status
        $results['status'] = $overallHealthy ? 'green' : 'red';

        return $results;
    }

    public function checkService(string $service): array
    {
        if (!isset($this->checkers[$service])) {
            return [
                'status' => 'error',
                'error' => sprintf('Service "%s" not found', $service),
                'timestamp' => date('c'),
                'available_services' => array_keys($this->checkers)
            ];
        }

        try {
            $result = $this->checkers[$service]->check();
            $result['status'] = $result['healthy'] ? 'healthy' : 'unhealthy';
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Health check exception for service: %s', $service), [
                'service' => $service,
                'exception' => $e->getMessage()
            ]);

            return [
                'status' => 'unhealthy',
                'healthy' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('c')
            ];
        }
    }

    public function getAvailableServices(): array
    {
        return array_keys($this->checkers);
    }

    private function getApplicationVersion(): string
    {
        // Try to read from composer.json or return default
        $composerPath = dirname(__DIR__, 6) . '/composer.json';
        
        if (file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
            return $composer['version'] ?? 'unknown';
        }

        return 'unknown';
    }
}
