<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Factory;

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ContentListFactory implements ContentListFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * ContentListFactory constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createTyped(string $type): ContentListInterface
    {
        /** @var ContentListInterface $contentList */
        $contentList = $this->create();
        $contentList->setType($type);

        return $contentList;
    }

    /**
     * {@inheritdoc}
     */
    public function create(): ContentListInterface
    {
        return $this->factory->create();
    }
}
