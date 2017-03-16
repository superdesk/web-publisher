<?php

declare(strict_types=1);

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

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentListBundle\Doctrine\ORM\ContentListItemRepository;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;

/**
 * @mixin ContentListItemRepository
 */
final class ContentListItemRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $entityManager, ClassMetadata $classMetadata, EventManager $em)
    {
        $em->getListeners()->willReturn([]);
        $entityManager->getEventManager()->willReturn($em);
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListItemRepository::class);
    }

    public function it_is_a_repository()
    {
        $this->shouldHaveType(ContentListItemRepository::class);
        $this->shouldImplement(ContentListItemRepositoryInterface::class);
    }
}
