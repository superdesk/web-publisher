<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Meta;

use Doctrine\Common\Collections\Collection;

interface MetaCollectionInterface extends Collection
{
    /**
     * Number of total existing in storage elements for current criteria.
     *
     * @var int
     */
    public function getTotalItemCount();
}
