<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Form\DataTransformer\OrganizationToCodeTransformer;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @mixin OrganizationToCodeTransformer
 */
class OrganizationToCodeTransformerSpec extends ObjectBehavior
{
    public function let(OrganizationRepositoryInterface $organizationRepository)
    {
        $this->beConstructedWith($organizationRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationToCodeTransformer::class);
    }

    public function it_should_return_empty_string_if_null_transformed()
    {
        $this->transform(null)->shouldReturn('');
    }

    public function it_should_throw_an_excpetion_when_wrong_object_is_given()
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringTransform(new \stdClass())
        ;
    }

    public function it_should_transform_organization_into_its_code(OrganizationInterface $organization)
    {
        $organization->getCode()->willReturn('123abc');

        $this->transform($organization)->shouldReturn('123abc');
    }

    public function it_should_return_null_if_empty_string_is_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    public function it_should_throw_exception_if_organization_not_found_on_reverse_transform(
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $organizationRepository
            ->findOneByCode('123abc')
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(TransformationFailedException::class)
            ->duringReverseTransform('123abc')
        ;
    }

    public function it_should_reverse_transform(
        OrganizationRepositoryInterface $organizationRepository,
        OrganizationInterface $organization
    ) {
        $organizationRepository
            ->findOneByCode('123abc')
            ->shouldBeCalled()
            ->willReturn($organization)
        ;

        $this->reverseTransform('123abc')->shouldReturn($organization);
    }
}
