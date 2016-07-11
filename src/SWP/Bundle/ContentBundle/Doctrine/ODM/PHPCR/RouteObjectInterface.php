<?php

namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;

interface RouteObjectInterface extends RouteInterface, HierarchyInterface
{
    /**
     * Set the object this url points to.
     *
     * @param mixed $object A content object that can be persisted by the
     *                      storage layer.
     */
    public function setContent($object);

    /**
     * Get the object this url points to.
     *
     * @return string
     */
    public function getContent();
}
