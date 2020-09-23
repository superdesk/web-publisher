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

namespace SWP\Bundle\ContentBundle\Hydrator;

use function count;
use SWP\Bundle\ContentBundle\Factory\MetadataFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleKeywordAdderInterface;
use SWP\Bundle\ContentBundle\Service\ArticleSourcesAdderInterface;
use SWP\Component\Bridge\Model\PackageInterface;

final class ArticleHydrator implements ArticleHydratorInterface
{
    /**
     * @var ArticleSourcesAdderInterface
     */
    private $articleSourcesAdder;

    /**
     * @var ArticleKeywordAdderInterface
     */
    private $articleKeywordAdder;

    /** @var MetadataFactoryInterface */
    private $metadataFactory;

    public function __construct(
        ArticleSourcesAdderInterface $articleSourcesAdder,
        ArticleKeywordAdderInterface $articleKeywordAdder,
        MetadataFactoryInterface $metadataFactory
    ) {
        $this->articleSourcesAdder = $articleSourcesAdder;
        $this->articleKeywordAdder = $articleKeywordAdder;
        $this->metadataFactory = $metadataFactory;
    }

    public function hydrate(ArticleInterface $article, PackageInterface $package): ArticleInterface
    {
        $article->setCode($package->getGuid());

        if (null === ($body = $package->getBody())) {
            $body = '';
        }

        $article->setBody($body);

        if (null !== $package->getSlugline() && null === $article->getSlug()) {
            $article->setSlug($package->getSlugline());
        }

        $article->setTitle($package->getHeadline());
        $article->setAuthors($package->getAuthors());
        $article->setExtra($package->getExtra());
        $article->setExtraFields($package->getExtra());

        $this->populateSources($article, $package);
        $this->populateKeywords($article, $package);

        $article->setLocale($package->getLanguage());
        $article->setLead($package->getDescription());
        $article->setData($this->metadataFactory->createFrom($package->getMetadata()));
        $article->setMetadata($package->getMetadata());

        return $article;
    }

    private function populateSources(ArticleInterface $article, PackageInterface $package): void
    {
        if (null === $package->getSource()) {
            return;
        }

        $this->articleSourcesAdder->add($article, $package->getSource());
    }

    private function populateKeywords(ArticleInterface $article, PackageInterface $package): void
    {
        if (0 === count($package->getKeywords())) {
            return;
        }

        foreach ($article->getKeywords() as $keyword) {
            $article->removeKeyword($keyword);
        }

        foreach ($package->getKeywords() as $keyword) {
            $this->articleKeywordAdder->add($article, $keyword);
        }
    }
}
