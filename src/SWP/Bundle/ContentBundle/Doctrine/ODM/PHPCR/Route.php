<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route as BaseRoute;

class Route extends BaseRoute implements RouteObjectInterface
{
    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $articlesTemplateName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $cacheTimeInSeconds = 0;

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
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticlesTemplateName()
    {
        return $this->articlesTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticlesTemplateName($articlesTemplateName)
    {
        $this->articlesTemplateName = $articlesTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getCacheTimeInSeconds()
    {
        return $this->cacheTimeInSeconds;
    }

    /**
     * @param int $cacheTimeInSeconds
     */
    public function setCacheTimeInSeconds($cacheTimeInSeconds)
    {
        $this->cacheTimeInSeconds = $cacheTimeInSeconds;
    }

    public function getRouteName()
    {
        return $this->getId();
    }
}
