<?php

/*
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

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\TemplatesSystemBundle\Model\Container as BaseContainer;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Revision\RevisionAwareTrait;

class Container extends BaseContainer implements ContainerInterface
{
    use TenantAwareTrait, RevisionAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function fork()
    {
        $container = clone $this;
        $container->setId(null);
        $container->setCreatedAt(new \DateTime());
        $container->setUpdatedAt(new \DateTime());

        return $container;
    }
}
