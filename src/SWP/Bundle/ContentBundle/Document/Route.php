<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Document;

use SWP\Bundle\MultiTenancyBundle\Document\Page;

class Route extends Page
{
    const TYPE_CONTENT = 'content';
    const TYPE_COLLECTION = 'collection';
}
