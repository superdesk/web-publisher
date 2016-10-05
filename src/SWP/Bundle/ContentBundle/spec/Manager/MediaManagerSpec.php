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

use Doctrine\ODM\PHPCR\DocumentManager;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Manager\MediaManager;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilder;
use League\Flysystem\Filesystem;
use Symfony\Component\Routing\Router;

class MediaManagerSpec extends ObjectBehavior
{
    public function let(
        TenantAwarePathBuilder $pathBuilder,
        Filesystem $filesystem,
        DocumentManager $objectManager,
        Router $router,
        TenantContextInterface $tenantContext
    ) {
        $this->beConstructedWith($pathBuilder, $filesystem, $objectManager, $router, $tenantContext, 'media');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MediaManager::class);
    }
}
