<?php
/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Document;

use SWP\Component\MultiTenancy\Model\RouteInterface;
use SWP\Component\MultiTenancy\Model\SiteDocumentInterface;

class Site implements SiteDocumentInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var RouteInterface
     */
    protected $homepage;

    /**
     * @var object
     */
    protected $children;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * {@inheritdoc}
     */
    public function setHomepage(RouteInterface $homepage)
    {
        $this->homepage = $homepage;
    }
}
