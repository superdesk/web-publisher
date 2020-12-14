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

use SWP\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;


class BaseMailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct($mailer, UrlGeneratorInterface $router, Environment $templating, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {

        $template = $this->parameters['confirmation.template'];
        $url = $this->router->generate('swp_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], (string) $user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['resetting.template'];
        $url = $this->router->generate('swp_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], (string) $user->getEmail());
    }

    /**
     * @param string       $renderedTemplate
     * @param array|string $fromEmail
     * @param array|string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
