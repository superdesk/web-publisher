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

use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Service\ArticleSourcesAdderInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\PackageInterface;

final class ArticleHydrator implements ArticleHydratorInterface
{
    /**
     * @var ArticleSourcesAdderInterface
     */
    private $articleSourcesAdder;

    /**
     * @var array
     */
    private $allowedTypes = [
        ItemInterface::TYPE_PICTURE,
        ItemInterface::TYPE_FILE,
        ItemInterface::TYPE_TEXT,
        ItemInterface::TYPE_COMPOSITE,
    ];

    /**
     * ArticleHydrator constructor.
     *
     * @param ArticleSourcesAdderInterface $articleSourcesAdder
     */
    public function __construct(ArticleSourcesAdderInterface $articleSourcesAdder)
    {
        $this->articleSourcesAdder = $articleSourcesAdder;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(ArticleInterface $article, PackageInterface $package): ArticleInterface
    {
        if ($this->populateByline($package) !== $package->getByLine()) {
            $package->setByLine($this->populateByline($package));
        }

        $article->setCode($package->getGuid());
        $article->setBody($this->populateBody($package));

        if (null !== $package->getSlugline() && null === $article->getSlug()) {
            $article->setSlug($package->getSlugline());
        }

        $article->setTitle($package->getHeadline());
        $article->setAuthors($package->getAuthors());
        $article->setExtra($package->getExtra());

        $this->populateSources($article, $package);

        $article->setLocale($package->getLanguage());
        $article->setLead($this->populateLead($package));
        $article->setMetadata($package->getMetadata());
        $article->setKeywords($package->getKeywords());
        $article->setRoute($article->getRoute());

        return $article;
    }

    /**
     * @param PackageInterface $package
     *
     * @return string
     */
    private function populateLead(PackageInterface $package)
    {
        if (null === $package->getDescription() || '' === $package->getDescription()) {
            $items = $this->filterTextItems($package->getItems());

            $map = $items->map(
                function (ItemInterface $item) {
                    return ' '.$item->getDescription();
                }
            );

            return trim($package->getDescription().implode('', $map->toArray()));
        }

        return $package->getDescription();
    }

    /**
     * @param PackageInterface $package
     *
     * @return string
     */
    private function populateByline(PackageInterface $package)
    {
        $items = $this->filterTextItems($package->getItems());

        $authors = array_filter(array_values(array_map(function (ItemInterface $item) {
            $metadata = $item->getMetadata();

            return $metadata['byline'];
        }, $items->toArray())));

        if (empty($authors)) {
            return $package->getByLine();
        }

        return implode(', ', $authors);
    }

    /**
     * @param Collection $items
     *
     * @return Collection
     */
    private function filterTextItems(Collection $items)
    {
        return $items->filter(
            function (ItemInterface $item) {
                $this->ensureTypeIsAllowed($item->getType());

                return ItemInterface::TYPE_TEXT === $item->getType();
            }
        );
    }

    /**
     * @param PackageInterface $package
     *
     * @return string
     */
    private function populateBody(PackageInterface $package)
    {
        return $package->getBody().' '.implode('', array_map(function (ItemInterface $item) {
            $this->ensureTypeIsAllowed($item->getType());

            return $item->getBody();
        }, $package->getItems()->toArray()));
    }

    /**
     * @param ArticleInterface $article
     * @param PackageInterface $package
     */
    private function populateSources(ArticleInterface $article, PackageInterface $package)
    {
        if (null === $package->getSource()) {
            return;
        }

        $this->articleSourcesAdder->add($article, $package->getSource());

        foreach ($package->getItems() as $item) {
            if (null === $item->getSource()) {
                continue;
            }

            $this->articleSourcesAdder->add($article, $item->getSource());
        }
    }

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    private function ensureTypeIsAllowed(string $type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Item type "%s" is not supported. Supported types are: %s',
                $type,
                implode(', ', $this->allowedTypes)
            ));
        }
    }
}
