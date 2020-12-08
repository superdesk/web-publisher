<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Mailer;

use Swift_Mailer;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;


class Mailer extends BaseMailer
{
    /**
     * Mailer constructor.
     *
     * @param Swift_Mailer            $mailer
     * @param UrlGeneratorInterface    $router
     * @param Environment          $templating
     * @param array                    $parameters
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(
        $mailer,
        UrlGeneratorInterface $router,
        Environment $templating,
        array $parameters,
        SettingsManagerInterface $settingsManager,
        TenantContextInterface $tenantContext
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;
        $tenant = $tenantContext->getTenant();

        if ($tenant instanceof SettingsOwnerInterface) {
            $fromEmail = ['contact@'.$tenant->getDomainName() => 'contact'];

            $this->parameters['confirmation.template'] = $settingsManager->get('registration_confirmation.template', 'tenant', $tenant);
            $this->parameters['from_email']['confirmation'] = $settingsManager->get('registration_from_email.confirmation', 'tenant', $tenant, $fromEmail);
            $this->parameters['resetting.template'] = $settingsManager->get('registration_resetting.template', 'tenant', $tenant);
            $this->parameters['from_email']['resetting'] = $settingsManager->get('registration_from_email.resetting', 'tenant', $tenant, $fromEmail);
        }
    }
}
