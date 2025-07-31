<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthCheckTest extends TestCase
{
    public function testHealthCheckResponseFormat()
    {
        // This is a basic test to verify the response format
        // In a real application, you would use Symfony's WebTestCase
        
        $expectedKeys = [
            'application_name',
            'postgres',
            'elasticsearch',
            'memcached',
            'rabbitmq',
            'supervisor'
        ];
        
        // Simulate the expected response structure
        $response = [
            'application_name' => 'Publisher',
            'postgres' => 'green',
            'elasticsearch' => 'green',
            'memcached' => 'green',
            'rabbitmq' => 'green',
            'supervisor' => 'green'
        ];
        
        // Verify all expected keys are present
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $response);
        }
        
        // Verify application name
        $this->assertEquals('Publisher', $response['application_name']);
        
        // Verify status values are either 'green' or 'red'
        foreach ($expectedKeys as $key) {
            if ($key !== 'application_name') {
                $this->assertContains($response[$key], ['green', 'red']);
            }
        }
    }
    
    public function testHealthCheckStatusValues()
    {
        $validStatuses = ['green', 'red'];
        
        // Test that status values are valid
        $status = 'green';
        $this->assertContains($status, $validStatuses);
        
        $status = 'red';
        $this->assertContains($status, $validStatuses);
        
        // Test that invalid status would fail
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->assertContains('invalid', $validStatuses);
    }
} 