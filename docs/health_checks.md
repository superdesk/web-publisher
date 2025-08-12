# Health Check System

This document describes the health check system implemented for the Superdesk Web Publisher application.

## Overview

The health check system provides monitoring endpoints to verify the health of all critical services used by the web publisher. This includes database connectivity, cache services, search engine, message queues, file storage, and external services.

## Features

- **Multiple endpoints**: Basic health check for load balancers and detailed checks for monitoring
- **Service-specific checks**: Individual service health verification
- **Comprehensive reporting**: Detailed service status with response times and error information
- **Configurable criticality**: Services are marked as critical or non-critical for overall health
- **Extensible design**: Easy to add new service checkers

## Endpoints

### Simple Health Check
```
GET /health
```
Basic health check endpoint suitable for load balancers. Returns minimal information.

**Example Response:**
```json
{
    "status": "healthy",
    "timestamp": "2023-12-07T10:30:00+00:00",
    "application": "Superdesk Web Publisher",
    "version": "2.2-dev"
}
```

### Detailed Health Check
```
GET /api/v2/health
```
Comprehensive health check of all services.

**Query Parameters:**
- `detailed=true` - Include detailed service information
- `service=<name>` - Check specific service only

**Example Response:**
```json
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
                "platform": "postgresql",
                "version": "13.8"
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
```

## Services Monitored

### Critical Services
These services are essential for basic application operation:

1. **Database (PostgreSQL)** - Primary data storage
2. **Cache (Memcached)** - Session storage and application cache

### Non-Critical Services
These services enhance functionality but don't prevent basic operation:

1. **Elasticsearch** - Search functionality
2. **RabbitMQ** - Message queue for background tasks
3. **S3 Storage** - File storage (if configured)
4. **Mailer** - Email service
5. **External HTTP Services** - Third-party integrations

## HTTP Status Codes

- `200 OK` - All critical services are healthy
- `503 Service Unavailable` - One or more critical services are unhealthy

## Configuration

Health checks are configured in `config/packages/health_check.yaml`. Services can be enabled/disabled and configured with custom parameters.

### Environment Variables

The following environment variables control health check behavior:

```bash
# Database
DATABASE_URL=pgsql://user:pass@host/database

# Cache
SESSION_MEMCACHED_HOST=localhost
SESSION_MEMCACHED_PORT=11211

# Elasticsearch
ELASTICA_HOST=localhost
ELASTICA_PORT=9200
ELASTICA_INDEX_NAME=swp_index

# RabbitMQ
RABBIT_MQ_HOST=127.0.0.1
RABBIT_MQ_PORT=5672
RABBIT_MQ_USER=guest
RABBIT_MQ_PASSWORD=guest
RABBIT_MQ_VHOST=/

# S3 Storage
FS_AWS_S3_BUCKET=your-bucket-name

# Email
FROM_EMAIL=contact@publisher.test
MAILER_DSN=smtp://localhost
```

## Adding New Service Checkers

To add a new service checker:

1. **Create a checker class** implementing `ServiceCheckerInterface`:

```php
<?php

namespace SWP\Bundle\CoreBundle\Service\HealthCheck\Checker;

class MyServiceChecker implements ServiceCheckerInterface
{
    public function check(): array
    {
        // Implement your health check logic
        return [
            'healthy' => true,
            'timestamp' => date('c'),
            'details' => [
                'response_time_ms' => 10.5
            ]
        ];
    }

    public function getName(): string
    {
        return 'my_service';
    }

    public function getDescription(): string
    {
        return 'My Custom Service';
    }

    public function isCritical(): bool
    {
        return false;
    }
}
```

2. **Register the service** in `config/packages/health_check.yaml`:

```yaml
services:
    swp_core.health_check.checker.my_service:
        class: SWP\Bundle\CoreBundle\Service\HealthCheck\Checker\MyServiceChecker
        arguments:
            - '@my_service_dependency'

    swp_core.health_check.service:
        calls:
            - ['addChecker', ['my_service', '@swp_core.health_check.checker.my_service']]
```

## Monitoring Integration

### Kubernetes Liveness/Readiness Probes

```yaml
livenessProbe:
  httpGet:
    path: /health
    port: 80
  initialDelaySeconds: 30
  periodSeconds: 10

readinessProbe:
  httpGet:
    path: /api/v2/health
    port: 80
  initialDelaySeconds: 5
  periodSeconds: 5
```

### Load Balancer Health Checks

Use the simple `/health` endpoint for load balancer health checks as it's lightweight and fast.

### External Monitoring

The detailed `/api/v2/health?detailed=true` endpoint provides comprehensive information suitable for external monitoring systems like:

- Prometheus
- New Relic
- DataDog
- Nagios

## Troubleshooting

### Common Issues

1. **Database connection failures**
   - Check `DATABASE_URL` configuration
   - Verify database server is running and accessible
   - Check network connectivity and firewall rules

2. **Cache connection failures**
   - Verify Memcached is running on configured host/port
   - Check `SESSION_MEMCACHED_HOST` and `SESSION_MEMCACHED_PORT` settings

3. **Elasticsearch connection failures**
   - Ensure Elasticsearch is running on `ELASTICA_HOST:ELASTICA_PORT`
   - Verify index `ELASTICA_INDEX_NAME` exists
   - Check cluster health status

4. **Service timeouts**
   - Increase timeout values in service configuration
   - Check network latency to service endpoints
   - Monitor service performance metrics

### Debug Mode

Enable detailed logging in development by setting log level to `debug` in `config/packages/dev/monolog.yaml`.

## Security Considerations

- Health check endpoints don't require authentication but should be restricted to monitoring networks
- Sensitive configuration details are not exposed in health check responses
- Consider rate limiting health check endpoints to prevent abuse
- Use HTTPS in production environments

## Performance Impact

Health checks are designed to be lightweight:
- Simple endpoint: ~1ms response time
- Detailed endpoint: ~50-100ms response time (depending on services)
- Minimal resource usage
- Connection pooling where supported

For high-traffic applications, consider:
- Caching health check results for 5-10 seconds
- Using separate monitoring instances
- Implementing circuit breakers for external services
