<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Document;

use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Component\MultiTenancy\Model\Organization as BaseOrganization;

class Organization extends BaseOrganization implements HierarchyInterface
{
    /**
     * PHPCR parent document.
     *
     * @var object
     */
    protected $parent;

    /**
     * {@inheritdoc}
     */
    public function setParent($parent)
    {
        $this->setParentDocument($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getParentDocument();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentDocument()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentDocument($parent)
    {
        if (!is_object($parent)) {
            throw new \InvalidArgumentException('Parent must be an object '.gettype($parent).' given.');
        }

        $this->parent = $parent;
    }
}
