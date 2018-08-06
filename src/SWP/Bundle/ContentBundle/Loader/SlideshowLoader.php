<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class SlideshowLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPES = ['slideshows', 'slideshow'];

    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

    /**
     * @var SlideshowRepositoryInterface
     */
    private $slideshowRepository;

    public function __construct(
        MetaFactoryInterface $metaFactory,
        SlideshowRepositoryInterface $slideshowRepository
    ) {
        $this->metaFactory = $metaFactory;
        $this->slideshowRepository = $slideshowRepository;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();

        if (LoaderInterface::SINGLE === $responseType) {
            if (array_key_exists('name', $parameters) && \is_string($parameters['name'])) {
                $criteria->set('name', $parameters['name']);
            } else {
                return false;
            }

            $slideshow = $this->slideshowRepository->findOneBy(['code' => $parameters['name']]);

            if (null !== $slideshow) {
                return $this->metaFactory->create($slideshow);
            }

            return false;
        }

        $slideshows = $this->slideshowRepository->getByCriteria($criteria, $criteria->get('order', []));

        if (0 === \count($slideshows)) {
            return false;
        }

        $slideshows = new ArrayCollection($slideshows);
        $criteria = $this->applyPaginationToCriteria($criteria, $parameters);

        $metaCollection = new MetaCollection();
        $metaCollection->setTotalItemsCount($this->slideshowRepository->countByCriteria($criteria));

        foreach ($slideshows as $slideshow) {
            $meta = $this->metaFactory->create($slideshow);
            if (null !== $meta) {
                $metaCollection->add($meta);
            }
        }
        unset($slideshows, $criteria);

        return $metaCollection;
    }

    public function isSupported(string $type): bool
    {
        return \in_array($type, self::SUPPORTED_TYPES, true);
    }
}
