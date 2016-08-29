<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface RouteToArticleInterface extends PersistableInterface
{
    /**
     * Set rule.
     *
     * @param string $rule
     *
     * @return RouteToArticle
     */
    public function setRule($rule);

    /**
     * Get rule.
     *
     * @return string
     */
    public function getRule();

    /**
     * Set routeId.
     *
     * @param string $routeId
     *
     * @return RouteToArticle
     */
    public function setRouteId($routeId);

    /**
     * Get routeId.
     *
     * @return string
     */
    public function getRouteId();

    /**
     * Set templateName.
     *
     * @param string $templateName
     *
     * @return RouteToArticle
     */
    public function setTemplateName($templateName);

    /**
     * Get templateName.
     *
     * @return string
     */
    public function getTemplateName();
}
