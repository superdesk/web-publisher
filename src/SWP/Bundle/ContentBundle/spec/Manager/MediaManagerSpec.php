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
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class MediaManagerSpec extends ObjectBehavior
{
    public function let(
        ArticleMediaRepositoryInterface $mediaRepository,
        Filesystem $filesystem,
        RouterInterface $router,
        FactoryInterface $imageFactory,
        FileFactoryInterface $fileFactory,
        MediaManagerInterface $mediaManager
    ) {
        $this->beConstructedWith($mediaRepository, $filesystem, $router, $fileFactory, $mediaManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MediaManager::class);
    }
}
