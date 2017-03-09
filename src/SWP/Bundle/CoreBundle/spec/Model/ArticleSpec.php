<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\Article as BaseArticle;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;

final class ArticleSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Article::class);
    }

    public function it_extends_base_article()
    {
        $this->shouldHaveType(BaseArticle::class);
    }

    public function it_should_implement_article_interface()
    {
        $this->shouldImplement(ArticleInterface::class);
    }

    public function it_has_no_organization_by_default()
    {
        $this->getOrganization()->shouldReturn(null);
    }

    public function its_organization_is_mutable(OrganizationInterface $organization)
    {
        $this->setOrganization($organization);
        $this->getOrganization()->shouldReturn($organization);
    }
}
