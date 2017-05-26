<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\Form\DataTransformer\TenantToCodeTransformer;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class TenantToCodeTransformerSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository, TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($tenantRepository, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantToCodeTransformer::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_should_return_null_if_null_transformed()
    {
        $this->transform(null)->shouldReturn(null);
    }

    public function it_should_throw_an_exception_when_tenant_not_found()
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringTransform(new \stdClass())
        ;
    }

    public function it_should_transform_tenant_to_code(TenantInterface $tenant)
    {
        $tenant->getId()->willReturn('123abc');

        $this->transform($tenant)->shouldReturn('123abc');
    }

    public function it_should_return_null_if_null_reverse_transformed()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    public function it_should_throw_an_exception_during_reverse_transform()
    {
        $this
            ->shouldThrow(TransformationFailedException::class)
            ->duringReverseTransform('')
        ;
    }

    public function it_should_reverse_transform_id_to_route(
        TenantRepositoryInterface $tenantRepository,
        TenantInterface $tenant,
        TenantContextInterface $tenantContext
    ) {
        $tenantRepository->findOneByCode('123abc')->willReturn($tenant);
        $tenantContext->setTenant($tenant)->shouldBeCalled();

        $this->reverseTransform('123abc')->shouldReturn($tenant);
    }
}
