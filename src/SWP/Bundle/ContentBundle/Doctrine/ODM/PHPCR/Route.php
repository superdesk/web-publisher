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
namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use SWP\Bundle\MultiTenancyBundle\Document\Page;

class Route extends Page implements RouteObjectInterface
{
    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $type;

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
}
