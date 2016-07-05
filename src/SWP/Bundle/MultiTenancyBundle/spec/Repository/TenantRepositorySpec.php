<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\MultiTenancyBundle\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TenantRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\MultiTenancyBundle\Repository\TenantRepository');
    }

    public function it_is_a_repository()
    {
        $this->shouldHaveType('Doctrine\ORM\EntityRepository');
        $this->shouldImplement('SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface');
    }

    public function it_finds_by_subdomain($entityManager, QueryBuilder $builder, AbstractQuery $query)
    {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('t')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 't', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->where('t.subdomain = :subdomain')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere('t.enabled = true')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('subdomain', 'example1')->shouldBeCalled()->willReturn($builder);
        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findBySubdomain('example1');
    }

    public function it_finds_available_tenants($entityManager, QueryBuilder $builder, AbstractQuery $query)
    {
        $entityManager->createQueryBuilder()->willReturn($builder);
        $builder->select('t')->willReturn($builder);
        $builder->from(Argument::any(), 't', Argument::cetera())->willReturn($builder);
        $builder->where('t.enabled = true')->willReturn($builder);
        $builder->getQuery()->willReturn($query);
        $query->getArrayResult()->shouldBeCalled();

        $this->findAvailableTenants();
    }
}
