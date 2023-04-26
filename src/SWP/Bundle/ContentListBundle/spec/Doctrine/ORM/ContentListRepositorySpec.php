<?php

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentListBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use SWP\Bundle\ContentListBundle\Doctrine\ORM\ContentListRepository;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;

/**
 * @mixin ContentListRepository
 */
final class ContentListRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListRepository::class);
    }

    public function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
        $this->shouldImplement(ContentListRepositoryInterface::class);
    }
}
