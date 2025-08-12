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
 * Class MailerChecker.
 */
class MailerChecker implements ServiceCheckerInterface
{
    private $mailer;
    private string $fromEmail;

    public function __construct($mailer, $transport = null, string $fromEmail = 'test@example.com')
    {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    public function check(): array
    {
        $startTime = microtime(true);
        
        try {
            // Check if mailer is available and get basic info
            $mailerClass = get_class($this->mailer);
            $isSwiftMailer = strpos($mailerClass, 'Swift') !== false;
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $details = [
                'response_time_ms' => $responseTime,
                'from_email' => $this->fromEmail,
                'mailer_class' => $mailerClass,
                'mailer_type' => $isSwiftMailer ? 'SwiftMailer' : 'Symfony Mailer',
            ];

            // For SwiftMailer, try to get transport info
            if ($isSwiftMailer && method_exists($this->mailer, 'getTransport')) {
                try {
                    $transport = $this->mailer->getTransport();
                    $details['transport_class'] = get_class($transport);
                    
                    // Test transport connectivity for SwiftMailer
                    if (method_exists($transport, 'isStarted')) {
                        $details['transport_started'] = $transport->isStarted();
                    }
                } catch (\Exception $e) {
                    $details['transport_error'] = $e->getMessage();
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
