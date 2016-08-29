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

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

/**
 * RouteToArticle.
 */
class RouteToArticle implements TenantAwareInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string
     */
    private $routeId;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    protected $tenantCode;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rule.
     *
     * @param string $rule
     *
     * @return RouteToArticle
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule.
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set routeId.
     *
     * @param string $routeId
     *
     * @return RouteToArticle
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;

        return $this;
    }

    /**
     * Get routeId.
     *
     * @return string
     */
    public function getRouteId()
    {
        return $this->routeId;
    }

    /**
     * Set templateName.
     *
     * @param string $templateName
     *
     * @return RouteToArticle
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * Get templateName.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenantCode()
    {
        return $this->tenantCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenantCode($code)
    {
        $this->tenantCode = $code;
    }
}
