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
class RouteToArticle implements TenantAwareInterface, RouteToArticleInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $rule;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $routeId;

    /**
     * @var string
     */
    protected $templateName;

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
     * {@inheritdoc}
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteId()
    {
        return $this->routeId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * {@inheritdoc}
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
