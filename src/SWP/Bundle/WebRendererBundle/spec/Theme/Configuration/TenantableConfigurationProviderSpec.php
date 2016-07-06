<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\WebRendererBundle\Theme\Configuration;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Theme\Configuration\TenantableConfigurationProvider;
use SWP\Bundle\WebRendererBundle\Theme\Helper\ThemeHelperInterface;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

/**
 * @mixin TenantableConfigurationProvider
 */
class TenantableConfigurationProviderSpec extends ObjectBehavior
{
    public function let(
        FileLocatorInterface $fileLocator,
        ConfigurationLoaderInterface $loader,
        ThemeHelperInterface $themeHelper
    ) {
        $this->beConstructedWith($fileLocator, $loader, 'testconfig.json', $themeHelper);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantableConfigurationProvider::class);
    }

    public function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    public function it_provides_loaded_configuration_files(
        FileLocatorInterface $fileLocator,
        ConfigurationLoaderInterface $loader,
        ThemeHelperInterface $themeHelper
    ) {
        $fileLocator->locateFilesNamed('testconfig.json')->willReturn([
            '/client1/testconfig.json',
            '/client2/testconfig.json',
        ]);

        $loader->load('/client1/testconfig.json')->willReturn(['name' => 'client1/custom-theme']);
        $loader->load('/client2/testconfig.json')->willReturn(['name' => 'client2/custom-theme']);

        $themeHelper->process(['name' => 'client1/custom-theme'])->willReturn(['name' => 'client1/custom-theme@client1']);
        $themeHelper->process(['name' => 'client2/custom-theme'])->willReturn(['name' => 'client2/custom-theme@client2']);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'client1/custom-theme@client1'],
            ['name' => 'client2/custom-theme@client2'],
        ]);
    }

    public function it_provides_an_empty_array_if_there_were_no_themes_found(FileLocatorInterface $fileLocator)
    {
        $fileLocator->locateFilesNamed('testconfig.json')
            ->willThrow(\InvalidArgumentException::class);

        $this
            ->getConfigurations()
            ->shouldReturn([]);
    }
}
