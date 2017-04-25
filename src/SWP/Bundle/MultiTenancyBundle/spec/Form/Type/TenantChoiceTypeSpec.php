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
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\Form\DataTransformer\TenantToCodeTransformer;
use SWP\Bundle\MultiTenancyBundle\Form\Type\TenantChoiceType;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

final class TenantChoiceTypeSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository, TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($tenantRepository, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantChoiceType::class);
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldHaveType(FormTypeInterface::class);
    }

    public function it_should_build_form(
        FormBuilderInterface $builder
    ) {
        $builder
            ->addModelTransformer(
                new CollectionToArrayTransformer()
            )->shouldBeCalled();

        $this->buildForm($builder, ['multiple' => true]);
    }

    public function it_has_block_prefix()
    {
        $this->getBlockPrefix()->shouldReturn('swp_tenant');
    }

    public function it_should_have_a_parent()
    {
        $this->getParent()->shouldReturn(ChoiceType::class);
    }
}
