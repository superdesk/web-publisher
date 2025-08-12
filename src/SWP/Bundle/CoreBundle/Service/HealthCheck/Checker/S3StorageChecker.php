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

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Class S3StorageChecker.
 */
class S3StorageChecker implements ServiceCheckerInterface
{
    private ?S3Client $s3Client;
    private string $bucketName;
    private bool $enabled;

    public function __construct(?S3Client $s3Client = null, string $bucketName = '', bool $enabled = true)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
        $this->enabled = $enabled;
    }

    public function check(): array
    {
        if (!$this->enabled || !$this->s3Client || empty($this->bucketName)) {
            return [
                'healthy' => true,
                'timestamp' => date('c'),
                'details' => [
                    'status' => 'disabled',
                    'message' => 'S3 storage is not configured or disabled'
                ]
            ];
        }

        $startTime = microtime(true);
        
        try {
            // Test bucket access
            $result = $this->s3Client->headBucket([
                'Bucket' => $this->bucketName
            ]);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $details = [
                'response_time_ms' => $responseTime,
                'bucket_name' => $this->bucketName,
                'region' => $this->s3Client->getRegion(),
            ];

            // Try to get bucket location
            try {
                $locationResult = $this->s3Client->getBucketLocation([
                    'Bucket' => $this->bucketName
                ]);
                $details['bucket_region'] = $locationResult['LocationConstraint'] ?? 'us-east-1';
            } catch (\Exception $e) {
                // Location info not critical
            }

            // Test write permission with a small test object
            try {
                $testKey = 'health-check/' . uniqid() . '.txt';
                $this->s3Client->putObject([
                    'Bucket' => $this->bucketName,
                    'Key' => $testKey,
                    'Body' => 'health check test',
                    'ContentType' => 'text/plain'
                ]);

                // Clean up test object
                $this->s3Client->deleteObject([
                    'Bucket' => $this->bucketName,
                    'Key' => $testKey
                ]);

                $details['write_permission'] = true;
            } catch (\Exception $e) {
                $details['write_permission'] = false;
                $details['write_error'] = $e->getMessage();
            }

            return [
                'healthy' => true,
                'timestamp' => date('c'),
                'details' => $details
            ];
            
        } catch (AwsException $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getAwsErrorMessage() ?: $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    'bucket_name' => $this->bucketName,
                    'aws_error_code' => $e->getAwsErrorCode(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'timestamp' => date('c'),
                'error' => $e->getMessage(),
                'details' => [
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    'bucket_name' => $this->bucketName,
                ]
            ];
        }
    }

    public function getName(): string
    {
        return 's3_storage';
    }

    public function getDescription(): string
    {
        return 'AWS S3 File Storage';
    }

    public function isCritical(): bool
    {
        return false; // File storage is important but not critical for basic operation
    }
}
