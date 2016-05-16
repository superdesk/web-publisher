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

namespace spec\SWP\Bundle\WebRendererBundle\Theme\Locator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\WebRendererBundle\Theme\Helper\PathHelperInterface;
use SWP\Bundle\WebRendererBundle\Theme\Locator\TenantableFileLocator;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @mixin TenantableFileLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TenantableFileLocatorSpec extends ObjectBehavior
{
    function let(FinderFactoryInterface $finderFactory, PathHelperInterface $helper)
    {
        $paths = ['/search/path/'];
        $helper->applySuffixFor($paths)->willReturn(['/search/path/tenant/']);
        $this->beConstructedWith($finderFactory, $paths, $helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TenantableFileLocator::class);
    }

    function it_implements_sylius_file_locator_interface()
    {
        $this->shouldImplement(FileLocatorInterface::class);
    }

    function it_searches_for_file(
        FinderFactoryInterface $finderFactory,
        Finder $finder,
        SplFileInfo $splFileInfo
    ) {
        $finderFactory->create()->willReturn($finder);
        $finder->name('theme.json')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/tenant/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);
        $finder->getIterator()->willReturn(new \ArrayIterator([
            $splFileInfo->getWrappedObject(),
        ]));

        $splFileInfo->getPathname()->willReturn('/search/path/tenant/nested/theme.json');
        $this->locateFileNamed('theme.json')->shouldReturn('/search/path/tenant/nested/theme.json');
    }

    function it_searches_for_files(
        FinderFactoryInterface $finderFactory,
        Finder $finder,
        SplFileInfo $firstSplFileInfo,
        SplFileInfo $secondSplFileInfo
    ) {
        $finderFactory->create()->willReturn($finder);
        $finder->name('theme.json')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/tenant/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);
        $finder->getIterator()->willReturn(new \ArrayIterator([
            $firstSplFileInfo->getWrappedObject(),
            $secondSplFileInfo->getWrappedObject(),
        ]));
        $firstSplFileInfo->getPathname()->willReturn('/search/path/tenant/nested1/theme.json');
        $secondSplFileInfo->getPathname()->willReturn('/search/path/tenant/nested2/theme.json');
        $this->locateFilesNamed('theme.json')->shouldReturn([
            '/search/path/tenant/nested1/theme.json',
            '/search/path/tenant/nested2/theme.json',
        ]);
    }

    function it_throws_an_exception_if_searching_for_file_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', ['']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', [null]);
    }
    function it_throws_an_exception_if_searching_for_files_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', ['']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', [null]);
    }

    function it_throws_an_exception_if_there_is_no_file_that_matches_the_given_name(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);
        $finder->name('theme.json')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/tenant/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);
        $finder->getIterator()->willReturn(new \ArrayIterator());
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', ['theme.json']);
    }

    function it_throws_an_exception_if_there_is_there_are_not_any_files_that_matches_the_given_name(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);
        $finder->name('theme.json')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/tenant/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);
        $finder->getIterator()->willReturn(new \ArrayIterator());
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', ['theme.json']);
    }

    function it_isolates_finding_paths_from_multiple_sources(
        FinderFactoryInterface $finderFactory,
        Finder $firstFinder,
        Finder $secondFinder,
        SplFileInfo $splFileInfo,
        PathHelperInterface $helper
    ) {
        $paths = ['/search/path/first/', '/search/path/second/'];
        $helper->applySuffixFor($paths)->willReturn(['/search/path/tenant/first/', '/search/path/tenant/second/']);
        $this->beConstructedWith($finderFactory, $paths, $helper);
        $finderFactory->create()->willReturn($firstFinder, $secondFinder);
        $firstFinder->name('theme.json')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->in('/search/path/tenant/first/')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->files()->shouldBeCalled()->willReturn($firstFinder);
        $secondFinder->name('theme.json')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->in('/search/path/tenant/second/')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->files()->shouldBeCalled()->willReturn($secondFinder);
        $firstFinder->getIterator()->willReturn(new \ArrayIterator([$splFileInfo->getWrappedObject()]));
        $secondFinder->getIterator()->willReturn(new \ArrayIterator());
        $splFileInfo->getPathname()->willReturn('/search/path/tenant/first/nested/theme.json');
        $this->locateFilesNamed('theme.json')->shouldReturn([
            '/search/path/tenant/first/nested/theme.json',
        ]);
    }

    function it_silences_finder_exceptions_even_if_searching_in_multiple_sources(
        FinderFactoryInterface $finderFactory,
        Finder $firstFinder,
        Finder $secondFinder,
        SplFileInfo $splFileInfo,
        PathHelperInterface $helper
    ) {
        $paths = ['/search/path/first/', '/search/path/second/'];
        $helper->applySuffixFor($paths)->willReturn(['/search/path/tenant/first/', '/search/path/tenant/second/']);
        $this->beConstructedWith($finderFactory, $paths, $helper);

        $finderFactory->create()->willReturn($firstFinder, $secondFinder);
        $firstFinder->name('theme.json')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->in('/search/path/tenant/first/')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->files()->shouldBeCalled()->willReturn($firstFinder);
        $secondFinder->name('theme.json')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->in('/search/path/tenant/second/')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->files()->shouldBeCalled()->willReturn($secondFinder);
        $firstFinder->getIterator()->willReturn(new \ArrayIterator([$splFileInfo->getWrappedObject()]));
        $secondFinder->getIterator()->willThrow(\InvalidArgumentException::class);
        $splFileInfo->getPathname()->willReturn('/search/path/tenant/first/nested/theme.json');
        $this->locateFilesNamed('theme.json')->shouldReturn([
            '/search/path/tenant/first/nested/theme.json',
        ]);
    }
}
