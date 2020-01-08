<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ReportMailer
{
    /** @var MailerInterface */
    private $mailer;

    /** @var string */
    private $fromEmail;

    public function __construct(MailerInterface $mailer, string $fromEmail)
    {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    public function sendReportReadyEmailNotification(string $email, string $reportUrl): void
    {
        $emailObject = (new Email())
            ->from($this->fromEmail)
            ->to($email)
            ->subject('Your report is ready!')
            ->text("You can download your report here $reportUrl");

        $this->mailer->send($emailObject);
    }
}
