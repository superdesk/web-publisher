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

/**
 * This mailer does nothing.
 * It is used when the 'email' configuration is not set,
 * and allows to use this bundle without swiftmailer.
 *
 */
class NoopMailer implements MailerInterface
{
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        // nothing happens.
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        // nothing happens.
    }
}
