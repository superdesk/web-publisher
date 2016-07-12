<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\StorageBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * @mixin EntityRepository
 */
class EntityRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(EntityRepository::class);
    }

    public function it_is_repository()
    {
        $this->shouldHaveType(\Doctrine\ORM\EntityRepository::class);
        $this->shouldImplement(RepositoryInterface::class);
    }

    public function it_should_add_object_to_repository(EntityManagerInterface $entityManager, PersistableInterface $object)
    {
        $entityManager->persist($object)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->add($object);
    }
}
