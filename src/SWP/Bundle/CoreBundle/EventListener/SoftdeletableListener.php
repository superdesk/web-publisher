<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gedmo\Sortable\Sortable;

final class SoftdeletableListener
{
    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if ($entity instanceof Sortable) {
            $entity->setPosition(-1);
            $om = $event->getObjectManager();
            $om->persist($entity);
            $om->flush();
        }
    }
}
