<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route;

class DoctrineMetadataListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();

        if ($classMetadata->isMappedSuperclass && Route::class === $classMetadata->getName()) {
            // restore indexes in mappedSuperclass Route
            // indexes should be set in the parent classes
            $classMetadata->setPrimaryTable(['indexes' => []]);
        }
    }
}
