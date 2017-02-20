<?php

/*
 * This file is part of the Superdesk Publisher Revision Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RevisionBundle\Model;

use SWP\Component\Revision\Model\Revision as BaseRevision;
use SWP\Component\Storage\Model\PersistableInterface;

class Revision extends BaseRevision implements PersistableInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }
}
