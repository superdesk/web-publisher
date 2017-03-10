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

namespace spec\SWP\Bundle\ContentListBundle\Form\Type;

use SWP\Bundle\ContentListBundle\Form\Type\ContentListTypeSelectorType;
use PhpSpec\ObjectBehavior;
use SWP\Component\ContentList\Model\ContentListInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin ContentListTypeSelectorType
 */
final class ContentListTypeSelectorTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListTypeSelectorType::class);
    }

    public function it_is_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    public function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn(ChoiceType::class);
    }

    public function it_has_a_block_prefix()
    {
        $this->getBlockPrefix()->shouldReturn('content_list_selector');
    }

    public function it_configures_options(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => [
                    'Automatic' => ContentListInterface::TYPE_AUTOMATIC,
                    'Manual' => ContentListInterface::TYPE_MANUAL,
                    'Bucket' => ContentListInterface::TYPE_BUCKET,
                ],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }
}
