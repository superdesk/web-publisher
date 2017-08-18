<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\ArticleSource as BaseArticleSource;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class ArticleSource extends BaseArticleSource implements ArticleSourceInterface
{
    use TenantAwareTrait;
}
