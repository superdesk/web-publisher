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

use Doctrine\Common\Collections\ArrayCollection;

class MetaCollection extends ArrayCollection implements MetaCollectionInterface
{
    /**
     * @var int
     */
    protected $totalItemsCount = 0;

    /**
     * {@inheritdoc}
     */
    public function getTotalItemCount()
    {
        if ($this->totalItemsCount === 0 && $this->count() > 0) {
            return $this->count();
        }

        return $this->totalItemsCount;
    }

    public function setTotalItemsCount($totalItemsCount)
    {
        $this->totalItemsCount = $totalItemsCount;

        return $this;
    }
}
