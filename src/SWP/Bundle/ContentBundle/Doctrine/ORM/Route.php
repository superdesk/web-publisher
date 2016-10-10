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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use SWP\Bundle\ContentBundle\Model\RouteTrait;
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route as BaseRoute;
use SWP\Component\Storage\Model\PersistableInterface;

class Route extends BaseRoute implements PersistableInterface
{
    use RouteTrait;

    /**
     * @var int
     */
    protected $id;

    public function getId()
    {
        return parent::getId();
    }
}
