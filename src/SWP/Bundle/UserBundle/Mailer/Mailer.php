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

use FOS\UserBundle\Mailer\Mailer as FOSMailer;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class Mailer extends FOSMailer
{
    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer         $mailer
     * @param UrlGeneratorInterface $router
     * @param EngineInterface       $templating
     * @param array                 $parameters
     */
    public function __construct(
        $mailer,
        UrlGeneratorInterface $router,
        EngineInterface $templating,
        array $parameters,
        SettingsManagerInterface $settingsManager
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;

        $this->parameters['confirmation.template'] = $settingsManager->get('registration_confirmation.template', 'tenant');
        $this->parameters['from_email']['confirmation'] = $settingsManager->get('registration_from_email.confirmation', 'tenant');
        $this->parameters['resetting.template'] = $settingsManager->get('registration_resetting.template', 'tenant');
        $this->parameters['from_email']['resetting'] = $settingsManager->get('registration_from_email.resetting', 'tenant');

        dump($this->parameters);
    }
}
