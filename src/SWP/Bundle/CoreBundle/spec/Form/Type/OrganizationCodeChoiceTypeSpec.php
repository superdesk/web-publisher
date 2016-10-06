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

namespace spec\SWP\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\CoreBundle\Form\DataTransformer\OrganizationToCodeTransformer;
use SWP\Bundle\CoreBundle\Form\Type\OrganizationCodeChoiceType;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin OrganizationCodeChoiceType
 */
class OrganizationCodeChoiceTypeSpec extends ObjectBehavior
{
    public function let(OrganizationRepositoryInterface $organizationRepository)
    {
        $this->beConstructedWith($organizationRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationCodeChoiceType::class);
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    public function it_defines_organization_choices(
        OptionsResolver $resolver,
        OrganizationRepositoryInterface $organizationRepository,
        OrganizationInterface $organization
    ) {
        $organizationRepository->findAvailable()->willReturn([$organization]);

        $resolver->setNormalizer('choices', Argument::type('callable'))->willReturn($resolver);
        $resolver->setDefaults([
            'invalid_message' => 'The selected organization does not exist',
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    public function it_should_have_parent()
    {
        $this->getParent()->shouldReturn(ChoiceType::class);
    }

    public function it_should_add_model_transformer(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(Argument::type(OrganizationToCodeTransformer::class))->shouldBeCalled();

        $this->buildForm($builder, []);
    }
}
