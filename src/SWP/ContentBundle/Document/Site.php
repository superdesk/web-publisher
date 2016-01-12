<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\ContentBundle\Document;

use SWP\MultiTenancyBundle\Document\SiteDocumentInterface;

class Site implements SiteDocumentInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Route
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
     * Gets the value of homepage.
     *
     * @return mixed
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Sets the value of homepage.
     *
     * @param mixed $homepage the homepage
     */
    public function setHomepage(Route $homepage)
    {
        $this->homepage = $homepage;
    }

    public function __toString()
    {
        return $this->id;
    }
}
