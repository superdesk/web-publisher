<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Manager;

use SWP\Bundle\FacebookInstantArticlesBundle\Model\ApplicationInterface;
use Facebook;

class FacebookManager
{
    /**
     * @var Facebook\Facebook
     */
    protected $facebook;

    public function createForApp(ApplicationInterface $application)
    {
        $this->facebook = new Facebook\Facebook([
            'app_id' => $application->getAppId(),
            'app_secret' => $application->getAppSecret(),
        ]);

        return $this->facebook;
    }
}
