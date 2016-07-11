<?php

namespace spec\SWP\Bundle\WebRendererBundle\Theme\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Theme\Factory\ThemeFactory;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

class ThemeFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeFactory::class);
    }

    public function it_implements_theme_factory_interface()
    {
        $this->shouldImplement(ThemeFactoryInterface::class);
    }

    public function it_creates_a_theme()
    {
        $this->create('example/theme@subdomain', '/theme/path')->shouldHaveNameAndPath('example/theme', '/theme/path');
        $this->create('example/theme', '/theme/path')->shouldHaveNameAndPath('example/theme', '/theme/path');
    }

    public function it_cant_create_a_theme()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('create', ['testtheme', '/theme/path']);
    }

    public function getMatchers()
    {
        return [
            'haveNameAndPath' => function (ThemeInterface $theme, $expectedName, $expectedPath) {
                return $expectedName === $theme->getName()
                && $expectedPath === $theme->getPath();
            },
        ];
    }
}
