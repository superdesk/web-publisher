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

interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation.
     */
    public function sendConfirmationEmailMessage(UserInterface $user);

    /**
     * Send an email to a user to confirm the password reset.
     */
    public function sendResettingEmailMessage(UserInterface $user);
}
