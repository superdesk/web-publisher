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

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TranslatableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;

interface ArticleInterface extends TimestampableInterface, TranslatableInterface, PersistableInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return \DateTime
     */
    public function getPublishedAt();

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @param string $templateName
     */
    public function setTemplateName($templateName);

    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @param string $templateName
     */
    public function setTemplateName($templateName);

    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route);

    /**
     * @return RouteInterface
     */
    public function getRoute();
}
