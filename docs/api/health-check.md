# Health Check Endpoint

## Overview

The health check endpoint provides a comprehensive status of all external services used by the Publisher application.

## Endpoint

```
GET /api/system/health
```

## Response Format

The endpoint returns a JSON response with the status of each service:

```json
{
  "application_name": "Publisher",
  "postgres": "green",
  "elasticsearch": "green", 
  "memcached": "green",
  "rabbitmq": "green",
  "supervisor": "green"
}
```

## Status Values

- `green`: Service is healthy and responding
- `red`: Service is unavailable or not responding

## Services Monitored

### PostgreSQL
- **Purpose**: Main database for the application
- **Check**: Attempts to connect to the database using Doctrine DBAL
- **Environment Variables**: `DATABASE_URL`

### Elasticsearch
- **Purpose**: Search functionality and content indexing
- **Check**: Verifies Elasticsearch client can connect and get status
- **Environment Variables**: `ELASTICA_HOST`, `ELASTICA_PORT`

### Memcached
- **Purpose**: Session storage and application caching
- **Check**: Performs a test write/read/delete operation
- **Environment Variables**: `MEMCACHED_DSN`, `SESSION_MEMCACHED_HOST`, `SESSION_MEMCACHED_PORT`

### RabbitMQ
- **Purpose**: Message queue for asynchronous processing
- **Check**: Attempts to connect using AMQP extension
- **Environment Variables**: `RABBIT_MQ_HOST`, `RABBIT_MQ_PORT`, `RABBIT_MQ_USER`, `RABBIT_MQ_PASSWORD`, `RABBIT_MQ_VHOST`

### Supervisor
- **Purpose**: Process management for worker processes
- **Check**: Verifies that all messenger consumer processes are running
- **Processes**: `messenger-consume:*`

## Error Handling

All health checks include error logging for debugging purposes. Failed checks are logged with the specific error message.

## Usage Examples

### Basic Health Check
```bash
curl -X GET http://localhost/api/system/health
```

### Health Check with Authentication (if required)
```bash
curl -X GET http://localhost/api/system/health \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Monitoring Integration

This endpoint can be integrated with monitoring systems like:
- Nagios
- Zabbix
- Prometheus
- Grafana
- AWS CloudWatch
- New Relic

## Troubleshooting

If services show as `red`, check:

1. **PostgreSQL**: Verify database is running and accessible
2. **Elasticsearch**: Check if Elasticsearch service is up
3. **Memcached**: Ensure Memcached is running on configured port
4. **RabbitMQ**: Verify RabbitMQ service and AMQP extension
5. **Supervisor**: Verify supervisor processes are configured and running

Check application logs for specific error messages from health check failures. 