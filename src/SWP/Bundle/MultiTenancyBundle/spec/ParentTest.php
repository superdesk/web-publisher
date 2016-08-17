<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\spec;

use Doctrine\ODM\PHPCR\HierarchyInterface;

class ParentTest implements HierarchyInterface
{
    protected $parent;

    public function getParentDocument()
    {
        return $this->parent;
    }

    public function getParent()
    {
        $this->getParentDocument();
    }

    public function setParentDocument($parent)
    {
        $this->parent = $parent;
    }

    public function setParent($parent)
    {
        $this->setParentDocument($parent);
    }
}
