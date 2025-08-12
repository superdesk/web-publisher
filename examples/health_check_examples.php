<?php

/**
 * Health Check System Examples
 * 
 * This file shows examples of how to use the health check system
 * in the Superdesk Web Publisher application.
 */

// Example 1: Basic health check usage in a controller or service
use SWP\Bundle\CoreBundle\Service\HealthCheck\HealthCheckServiceInterface;

class ExampleUsage
{
    private HealthCheckServiceInterface $healthCheckService;

    public function __construct(HealthCheckServiceInterface $healthCheckService)
    {
        $this->healthCheckService = $healthCheckService;
    }

    /**
     * Example: Check all services
     */
    public function checkAllServices(): array
    {
        return $this->healthCheckService->checkAll(true); // detailed = true
    }

    /**
     * Example: Check specific service
     */
    public function checkDatabase(): array
    {
        return $this->healthCheckService->checkService('database');
    }

    /**
     * Example: Get available services
     */
    public function getAvailableServices(): array
    {
        return $this->healthCheckService->getAvailableServices();
    }

    /**
     * Example: Basic health check (minimal)
     */
    public function basicHealthCheck(): array
    {
        return $this->healthCheckService->checkBasic();
    }
}

/**
 * Example API calls using curl:
 */

/*
# Basic health check (suitable for load balancers)
curl http://localhost/health

# Detailed health check
curl http://localhost/api/v2/health?detailed=true

# Check specific service
curl http://localhost/api/v2/health?service=database

# Expected responses:

## Healthy response (200 OK):
{
    "status": "healthy",
    "timestamp": "2023-12-07T10:30:00+00:00",
    "application": "Superdesk Web Publisher",
    "version": "2.2-dev",
    "services": {
        "database": {
            "healthy": true,
            "timestamp": "2023-12-07T10:30:00+00:00",
            "details": {
                "response_time_ms": 15.2,
                "driver": "pdo_pgsql",
                "platform": "postgresql"
            }
        },
        "cache": {
            "healthy": true,
            "timestamp": "2023-12-07T10:30:00+00:00",
            "details": {
                "response_time_ms": 2.1,
                "host": "localhost",
                "port": 11211
            }
        }
    }
}

## Unhealthy response (503 Service Unavailable):
{
    "status": "unhealthy",
    "timestamp": "2023-12-07T10:30:00+00:00",
    "application": "Superdesk Web Publisher",
    "version": "2.2-dev",
    "services": {
        "database": {
            "healthy": false,
            "timestamp": "2023-12-07T10:30:00+00:00",
            "error": "Connection refused",
            "details": {
                "response_time_ms": 5000.0
            }
        }
    }
}
*/

/**
 * Example: Adding a custom health checker
 */

use SWP\Bundle\CoreBundle\Service\HealthCheck\Checker\ServiceCheckerInterface;

class CustomServiceChecker implements ServiceCheckerInterface
{
    private string $apiUrl;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Your custom health check logic here
            $response = file_get_contents($this->apiUrl, false, stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'GET'
                ]
            ]));
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'healthy' => true,
                'timestamp' => date('c'),
                'details' => [
                    'response_time_ms' => $responseTime,
                    'api_url' => $this->apiUrl,
                    'response_length' => strlen($response)
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    'api_url' => $this->apiUrl
                ]
            ];
        }
    }

    public function getName(): string
    {
        return 'custom_api';
    }

    public function getDescription(): string
    {
        return 'Custom API Service';
    }

    public function isCritical(): bool
    {
        return false;
    }
}

/**
 * Service configuration for custom checker:
 * 
 * # config/packages/health_check.yaml
 * services:
 *     swp_core.health_check.checker.custom_api:
 *         class: Path\To\CustomServiceChecker
 *         arguments:
 *             - '%env(CUSTOM_API_URL)%'
 * 
 *     swp_core.health_check.service:
 *         calls:
 *             - ['addChecker', ['custom_api', '@swp_core.health_check.checker.custom_api']]
 */

/**
 * Monitoring integration examples:
 */

/*
# Kubernetes liveness probe
livenessProbe:
  httpGet:
    path: /health
    port: 80
  initialDelaySeconds: 30
  periodSeconds: 10
  timeoutSeconds: 5
  failureThreshold: 3

# Kubernetes readiness probe  
readinessProbe:
  httpGet:
    path: /api/v2/health
    port: 80
  initialDelaySeconds: 5
  periodSeconds: 5
  timeoutSeconds: 3
  failureThreshold: 2

# Docker health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/health || exit 1

# Load balancer health check (HAProxy)
option httpchk GET /health
http-check expect status 200

# Prometheus monitoring (using http_2xx probe)
- job_name: 'web-publisher-health'
  static_configs:
    - targets: ['your-app.example.com']
  metrics_path: /api/v2/health
  params:
    detailed: ['true']
*/
