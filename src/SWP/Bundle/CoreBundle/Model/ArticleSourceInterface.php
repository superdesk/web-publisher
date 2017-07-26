<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface as BaseArticleSourceInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

interface ArticleSourceInterface extends BaseArticleSourceInterface, TenantAwareInterface
{
}
