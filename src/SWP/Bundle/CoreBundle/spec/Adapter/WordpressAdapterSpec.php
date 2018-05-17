<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\CoreBundle\Adapter\AdapterInterface;
use SWP\Bundle\CoreBundle\Adapter\WordpressAdapter;
use PhpSpec\ObjectBehavior;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class WordpressAdapterSpec extends ObjectBehavior
{
    public function let(
        ClientInterface $client,
        RepositoryInterface $externalArticleRepository,
        EntityManagerInterface $externalArticleManager,
        MediaManagerInterface $mediaManager
    ) {
        $this->beConstructedWith($client, $externalArticleRepository, $externalArticleManager, $mediaManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(WordpressAdapter::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(AdapterInterface::class);
    }
}
