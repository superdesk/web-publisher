<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use SWP\Bundle\ContentBundle\Doctrine\Types\EmptyStringOrNullStringType;
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route as CmfRoute;

/**
 * Adjusts the inherited CMF Route mapping to match the existing schema:
 *
 *  - The typed CMF Route::$staticPrefix property defaults to '' instead of
 *    null; the (staticprefix, tenant_code) unique index relies on unset
 *    prefixes being NULL, so the column is remapped to a type that stores
 *    empty strings as NULL.
 *  - Newer CMF maps Route::$condition to a (reserved-word) `condition`
 *    column, while the schema has always used `condition_expr`; the column
 *    is remapped to keep existing data and avoid the reserved word.
 */
final class RouteStaticPrefixMetadataListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (!is_a($classMetadata->getName(), CmfRoute::class, true)) {
            return;
        }

        if (isset($classMetadata->fieldMappings['staticPrefix'])) {
            $classMetadata->fieldMappings['staticPrefix']['type'] = EmptyStringOrNullStringType::NAME;
            $classMetadata->fieldMappings['staticPrefix']['nullable'] = true;
        }

        if (isset($classMetadata->fieldMappings['condition'])) {
            $classMetadata->fieldMappings['condition']['columnName'] = 'condition_expr';
            $classMetadata->fieldMappings['condition']['nullable'] = true;
        }
    }
}
