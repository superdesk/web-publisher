<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Manager;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\FileFactoryInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManager;
use League\Flysystem\Filesystem;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class MediaManagerSpec extends ObjectBehavior
{
    public function let(
        ArticleMediaRepositoryInterface $mediaRepository,
        Filesystem $filesystem,
        Router $router,
        FileFactoryInterface $fileFactory,
        AssetLocationResolverInterface $assetLocationResolver
    ): void {
        $router->getContext()->willReturn(new RequestContext());
        $assetLocationResolver->getAssetUrl(Argument::type(FileInterface::class))->willReturn(
            'https://localhost.test/swp/media/file.png',
            'swp/media/file.png',
            'swp/media/file.png'
        );
        $this->beConstructedWith($mediaRepository, $filesystem, $router, $fileFactory, $assetLocationResolver);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(MediaManager::class);
    }

    public function it_get_correct_media_uri(FileInterface $file): void
    {
        $this->getMediaUri($file)->shouldReturn('https://localhost.test/swp/media/file.png');
        $this->getMediaUri($file)->shouldReturn('/swp/media/file.png');
        $this->getMediaUri($file, RouterInterface::ABSOLUTE_URL)->shouldReturn('http://localhost/swp/media/file.png');
    }
}
