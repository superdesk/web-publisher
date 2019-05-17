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
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\FileFactoryInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManager;
use League\Flysystem\Filesystem;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class MediaManagerSpec extends ObjectBehavior
{
    public function let(
        ArticleMediaRepositoryInterface $mediaRepository,
        Filesystem $filesystem,
        Router $router,
        FileFactoryInterface $fileFactory,
        AssetLocationResolverInterface $assetLocationResolver
    ) {
        $this->beConstructedWith($mediaRepository, $filesystem, $router, $fileFactory, $assetLocationResolver);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MediaManager::class);
    }
}
