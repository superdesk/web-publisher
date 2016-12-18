<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Provider\ORM;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\ORM\ArticleProvider;
use SWP\Component\Common\Criteria\Criteria;

final class ArticleProviderSpec extends ObjectBehavior
{
    function let(ArticleRepositoryInterface $articleRepository)
    {
        $this->beConstructedWith($articleRepository);
    }

    function it_should_implement_interface()
    {
        $this->shouldImplement(ArticleProviderInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleProvider::class);
    }

    function it_counts_by_criteria(ArticleRepositoryInterface $articleRepository)
    {
        $criteria = new Criteria(['key' => 'value']);
        $articleRepository->countByCriteria($criteria)->willReturn(1);

        $this->getCountByCriteria($criteria)->shouldReturn(1);
    }
}
