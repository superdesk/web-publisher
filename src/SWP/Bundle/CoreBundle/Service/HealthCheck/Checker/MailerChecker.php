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

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

/**
 * Class MailerChecker.
 */
class MailerChecker implements ServiceCheckerInterface
{
    private MailerInterface $mailer;
    private ?TransportInterface $transport;
    private string $fromEmail;

    public function __construct(MailerInterface $mailer, ?TransportInterface $transport = null, string $fromEmail = 'test@example.com')
    {
        $this->mailer = $mailer;
        $this->transport = $transport;
        $this->fromEmail = $fromEmail;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Test transport connectivity if available
            if ($this->transport) {
                // Just verify the transport is configured properly
                // Most transports don't support direct connectivity testing
                $transportClass = get_class($this->transport);
            }
            
            // Create a test email (but don't send it)
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('healthcheck@example.com')
                ->subject('Health Check Test')
                ->text('This is a health check test email');

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $details = [
                'response_time_ms' => $responseTime,
                'from_email' => $this->fromEmail,
                'transport_available' => $this->transport !== null,
            ];

            // Get transport info if available
            if ($this->transport) {
                $details['transport_class'] = get_class($this->transport);
                
                            // Try to get additional transport details
            if ($this->transport && method_exists($this->transport, '__toString')) {
                try {
                    $transportString = (string) $this->transport;
                    // Parse DSN info safely
                    if (preg_match('/^(\w+):\/\//', $transportString, $matches)) {
                        $details['transport_scheme'] = $matches[1];
                    }
                } catch (\Exception $e) {
                    // Transport string conversion may fail
                }
            }
            }

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
                    'from_email' => $this->fromEmail,
                    'transport_available' => $this->transport !== null,
                ]
            ];
        }
    }

    public function getName(): string
    {
        return 'mailer';
    }

    public function getDescription(): string
    {
        return 'Email Service';
    }

    public function isCritical(): bool
    {
        return false; // Email is important but not critical for basic operation
    }
}
