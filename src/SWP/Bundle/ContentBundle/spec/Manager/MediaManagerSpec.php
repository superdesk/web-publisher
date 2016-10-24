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
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManager;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use League\Flysystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;

class MediaManagerSpec extends ObjectBehavior
{
    public function let(
        ArticleMediaRepositoryInterface $mediaRepository,
        MediaFactoryInterface $mediaFactory,
        Filesystem $filesystem,
        RouterInterface $router,
        TenantContextInterface $tenantContext
    ) {
        $this->beConstructedWith($mediaRepository, $mediaFactory, $filesystem, $router, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MediaManager::class);
    }
}
