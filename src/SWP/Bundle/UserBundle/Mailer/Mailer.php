<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Mailer;

use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class Mailer implements \SWP\Bundle\UserBundle\Mailer\MailerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        MailerInterface $mailer,
        array $parameters,
        SettingsManagerInterface $settingsManager,
        TenantContextInterface $tenantContext
    )
    {
        $this->mailer = $mailer;
        $this->parameters = $parameters;
        $tenant = $tenantContext->getTenant();

        if ($tenant instanceof SettingsOwnerInterface) {
            $fromEmail = ['contact@' . $tenant->getDomainName() => 'contact'];

            $this->parameters['confirmation.template'] =
                $settingsManager->get('registration_confirmation.template', 'tenant', $tenant);
            $this->parameters['from_email']['confirmation'] =
                $settingsManager->get('registration_from_email.confirmation', 'tenant', $tenant, $fromEmail);
            $this->parameters['resetting.template'] =
                $settingsManager->get('registration_resetting.template', 'tenant', $tenant);
            $this->parameters['from_email']['resetting'] =
                $settingsManager->get('registration_from_email.resetting', 'tenant', $tenant, $fromEmail);
        }
    }

    public function sendConfirmationEmail(UserInterface $user, $url): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getAdminAddress())
            ->to($user->getEmail())
            ->subject(sprintf('Welcome %s!', $user->getUsername()))
            ->htmlTemplate($this->parameters['confirmation.template']);

        $context = $email->getContext();
        $context['url'] = $url;
        $email->context($context);
        $this->mailer->send($email);
    }

    public function sendResetPasswordEmail(UserInterface $user, ResetPasswordToken $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getAdminAddress())
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('@SWPUser/reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->mailer->send($email);
    }

    /**
     * @return Address
     */
    private function getAdminAddress(): Address
    {
        return new Address(
            key($this->parameters['from_email']['resetting']),
            reset($this->parameters['from_email']['resetting']));
    }
}
