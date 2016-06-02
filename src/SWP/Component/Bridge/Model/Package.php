<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Package extends BaseContent implements PackageInterface
{
    /**
     * @var ItemInterface[]
     */
    protected $items;

    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }
}
