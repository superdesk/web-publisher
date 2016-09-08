<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\Form\Type;

use SWP\Bundle\ContentBundle\Form\DataTransformer\ArticleToIdTransformer;
use SWP\Bundle\ContentBundle\Form\Type\ArticleSelectorType;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin ArticleSelectorType
 */
final class ArticleSelectorTypeSpec extends ObjectBehavior
{
    function let(ArticleProviderInterface $articleProvider)
    {
        $this->beConstructedWith($articleProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleSelectorType::class);
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType(FormTypeInterface::class);
    }

    function it_should_build_form(FormBuilderInterface $builder, ArticleProviderInterface $articleProvider)
    {
        $builder
            ->addModelTransformer(
                new ArticleToIdTransformer($articleProvider->getWrappedObject())
            )->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_should_set_defaults(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['invalid_message' => 'The selected article does not exist!'])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    function it_should_have_a_parent()
    {
        $this->getParent()->shouldReturn(TextType::class);
    }
}
