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

use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface as BaseRouteObjectInterface;

/**
 * Interface RouteObjectInterface.
 */
interface RouteObjectInterface extends RouteInterface, HierarchyInterface, BaseRouteObjectInterface
{
    /**
     * Set the object this url points to.
     *
     * @param mixed $object A content object that can be persisted by the
     *                      storage layer.
     */
    public function setContent($object);
}
