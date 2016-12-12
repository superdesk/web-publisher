<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Factory\AbstractArticleFactory;
use SWP\Bundle\ContentBundle\Hydrator\ArticleHydratorInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ArticleFactory extends AbstractArticleFactory
{
    /**
     * @var FactoryInterface
     */
    protected $baseFactory;

    /**
     * ArticleFactory constructor.
     *
     * @param FactoryInterface         $baseFactory
     * @param ArticleHydratorInterface $articleHydrator
     */
    public function __construct(
        FactoryInterface $baseFactory,
        ArticleHydratorInterface $articleHydrator
    ) {
        parent::__construct($articleHydrator);

        $this->baseFactory = $baseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->baseFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function createFromPackage(PackageInterface $package)
    {
        return $this->createHydrated($package);
    }
}
