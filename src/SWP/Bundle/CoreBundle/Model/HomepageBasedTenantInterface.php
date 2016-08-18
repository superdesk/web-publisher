<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\RouteObjectInterface;

interface HomepageBasedTenantInterface
{
    /**
     * Gets the homepage.
     *
     * @return RouteObjectInterface
     */
    public function getHomepage();

    /**
     * Sets the homepage.
     *
     * @param RouteObjectInterface $homepage
     */
    public function setHomepage(RouteObjectInterface $homepage);
}
