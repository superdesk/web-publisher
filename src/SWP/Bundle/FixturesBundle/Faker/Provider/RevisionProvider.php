<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\Faker\Provider;

use SWP\Bundle\CoreBundle\Model\RevisionInterface;
use SWP\Component\Revision\Repository\RevisionRepositoryInterface;

class RevisionProvider
{
    protected $revisionRepository;

    public function __construct(RevisionRepositoryInterface $revisionRepository)
    {
        $this->revisionRepository = $revisionRepository;
    }

    public function getPublishedRevision(): ?RevisionInterface
    {
        return $this->revisionRepository->getPublishedRevision()->getQuery()->getOneOrNullResult();
    }
}
