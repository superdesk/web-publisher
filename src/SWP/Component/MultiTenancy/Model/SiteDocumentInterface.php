<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\MultiTenancy\Model;

/**
 * Site Document interface.
 */
interface SiteDocumentInterface
{
    /**
     * Gets the site identifier.
     *
     * @return string The site identifier
     */
    public function getId();

    /**
     * Sets the site identifier/path.
     *
     * @param string $id The site identifier
     */
    public function setId($id);

    /**
     * Gets the homepage.
     *
     * @return RouteInterface
     */
    public function getHomepage();

    /**
     * Sets the homepage.
     * 
     * @param RouteInterface $homepage
     */
    public function setHomepage(RouteInterface $homepage);
}
