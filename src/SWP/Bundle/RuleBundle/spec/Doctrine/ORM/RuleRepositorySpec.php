<?php

/**
 * This file is part of the Superdesk Web Publisher Rule Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\RuleBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use SWP\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;

/**
 * @mixin RuleRepository
 */
final class RuleRepositorySpec extends ObjectBehavior
{
    function let(EntityManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RuleRepository::class);
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
        $this->shouldImplement(RuleRepositoryInterface::class);
    }
}
