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

namespace SWP\Bundle\FacebookInstantArticlesBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface PageInterface extends PersistableInterface
{
    /**
     * @return string
     */
    public function getPageId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken);

    /**
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * @return ApplicationInterface
     */
    public function getApplication();
}
