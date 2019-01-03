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
use SWP\Bundle\ContentBundle\Doctrine\ArticleAuthorRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class ArticleAuthorLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPES = ['authors', 'author'];

    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

    /**
     * @var ArticleAuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * ArticleAuthorLoader constructor.
     *
     * @param MetaFactoryInterface             $metaFactory
     * @param ArticleAuthorRepositoryInterface $authorRepository
     */
    public function __construct(
        MetaFactoryInterface $metaFactory,
        ArticleAuthorRepositoryInterface $authorRepository
    ) {
        $this->metaFactory = $metaFactory;
        $this->authorRepository = $authorRepository;
    }

    /**
     *  {@inheritdoc}
     */
    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        if ('author' === $type && LoaderInterface::SINGLE === $responseType) {
            $criteria = new Criteria();
            if (array_key_exists('id', $parameters) && is_numeric($parameters['id'])) {
                $criteria->set('id', $parameters['id']);
            } elseif (array_key_exists('name', $parameters) && \is_string($parameters['name'])) {
                $criteria->set('name', $parameters['name']);
            } elseif (array_key_exists('slug', $parameters) && \is_string($parameters['slug'])) {
                $criteria->set('slug', $parameters['slug']);
            } else {
                return false;
            }

            $criteria->set('maxResults', 1);
            $author = $this->authorRepository->getQueryByCriteria($criteria, [], 'a')->getQuery()->getOneOrNullResult();

            if (null !== $author) {
                return $this->metaFactory->create($author);
            }

            return false;
        }

        $criteria = new Criteria($parameters);
        foreach ($withoutParameters as $key => $withoutParameter) {
            $criteria->set('exclude_'.$key, $withoutParameter[0]);
        }

        $this->applyPaginationToCriteria($criteria, $parameters);
        $authors = $this->authorRepository->getByCriteria($criteria, $criteria->get('order', []));
        $authors = new ArrayCollection($authors);
        $countCriteria = clone $criteria;

        if (0 === $authors->count()) {
            return false;
        }

        $metaCollection = new MetaCollection();
        $metaCollection->setTotalItemsCount($this->authorRepository->countByCriteria($countCriteria));
        foreach ($authors as $author) {
            $meta = $this->metaFactory->create($author);
            if (null !== $meta) {
                $metaCollection->add($meta);
            }
        }
        unset($authors, $criteria);

        return $metaCollection;
    }

    /**
     *  {@inheritdoc}
     */
    public function isSupported(string $type): bool
    {
        return \in_array($type, self::SUPPORTED_TYPES, true);
    }
}
