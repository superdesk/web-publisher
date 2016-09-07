<?php

/**
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
use SWP\Bundle\CoreBundle\Form\Type\ThemeNameChoiceType;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin ThemeNameChoiceType
 */
class ThemeNameChoiceTypeSpec extends ObjectBehavior
{
    public function let(ThemeProviderInterface $themeProvider)
    {
        $this->beConstructedWith($themeProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeNameChoiceType::class);
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    public function it_defines_themes_names_choices(
        OptionsResolver $resolver,
        ThemeProviderInterface $themeProvider,
        ThemeInterface $theme
    ) {
        $theme->getName()->willReturn('swp/theme-name');
        $themeProvider->getCurrentTenantAvailableThemes()->willReturn([$theme]);

        $resolver->setNormalizer('choices', Argument::type('callable'))->willReturn($resolver);
        $resolver->setDefaults(['invalid_message' => 'The selected theme does not exist'])->shouldBeCalled();
        $this->configureOptions($resolver);
    }

    public function it_should_have_parent()
    {
        $this->getParent()->shouldReturn(ChoiceType::class);
    }
}
