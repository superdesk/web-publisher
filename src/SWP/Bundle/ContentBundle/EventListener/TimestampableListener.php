<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Doctrine\TimestampableCancelInterface;
use Gedmo\Timestampable\TimestampableListener as GedmoTimestampableListener;

class TimestampableListener extends GedmoTimestampableListener
{
    protected function updateField($object, $eventAdapter, $meta, $field)
    {
        if (!$this->isTimestampableCanceled($object)) {
            parent::updateField($object, $eventAdapter, $meta, $field);
        }
    }

    private function isTimestampableCanceled($object): bool
    {
        if (!$object instanceof TimestampableCancelInterface) {
            return false;
        }

        return $object->isTimestampableCanceled();
    }
}
