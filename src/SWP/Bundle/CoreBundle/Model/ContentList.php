<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Doctrine\ORM\TimestampableCancelTrait;
use SWP\Component\ContentList\Model\ContentList as BaseContentList;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class ContentList extends BaseContentList implements ContentListInterface, ContentListItemsCountInterface
{
    use TenantAwareTrait, ContentListItemsCountTrait, TimestampableCancelTrait;
}
