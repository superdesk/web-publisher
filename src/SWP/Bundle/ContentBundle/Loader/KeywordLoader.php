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

use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

final class KeywordLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPES = ['keyword'];

    public const PARAM_NAME_SLUG = 'slug';

    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

    /**
     * @var RepositoryInterface
     */
    private $keywordRepository;

    public function __construct(
        MetaFactoryInterface $metaFactory,
        RepositoryInterface $keywordRepository
    ) {
        $this->metaFactory = $metaFactory;
        $this->keywordRepository = $keywordRepository;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();

        if (LoaderInterface::SINGLE === $responseType) {
            if (array_key_exists(self::PARAM_NAME_SLUG, $parameters) && \is_string($parameters[self::PARAM_NAME_SLUG])) {
                $criteria->set(self::PARAM_NAME_SLUG, $parameters[self::PARAM_NAME_SLUG]);
            } else {
                return false;
            }

            $keyword = $this->keywordRepository->findOneBySlug($parameters[self::PARAM_NAME_SLUG]);

            if (null !== $keyword) {
                return $this->metaFactory->create($keyword);
            }
        }

        return false;
    }

    public function isSupported(string $type): bool
    {
        return \in_array($type, self::SUPPORTED_TYPES, true);
    }
}
