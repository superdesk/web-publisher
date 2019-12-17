<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Loader;

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * Class RenditionLoader.
 */
class RenditionLoader implements LoaderInterface
{
    /**
     * Fallback rendition name.
     */
    const FALLBACK_NAME = 'original';

    /**
     * @var Context
     */
    protected $templateContext;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    public function __construct(Context $templateContext, MetaFactoryInterface $metaFactory)
    {
        $this->templateContext = $templateContext;
        $this->metaFactory = $metaFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($metaType, $parameters = [], $withoutParameters = [], $responseType = self::SINGLE)
    {
        if (LoaderInterface::SINGLE === $responseType) {
            $renditionName = null;
            $fallbackRenditionName = self::FALLBACK_NAME;
            $fallbackRendition = null;

            if (array_key_exists('media', $parameters) && $parameters['media'] instanceof Meta) {
                $articleMedia = $parameters['media']->getValues();
            } else {
                if (!$this->templateContext['articleMedia'] instanceof Meta) {
                    return false;
                }

                /** @var ArticleMediaInterface $articleMedia */
                $articleMedia = $this->templateContext['articleMedia']->getValues();
            }

            if (array_key_exists('fallback', $parameters) && is_string($parameters['fallback'])) {
                $fallbackRenditionName = $parameters['fallback'];
            }

            if (array_key_exists('name', $parameters) && is_string($parameters['name'])) {
                $renditionName = $parameters['name'];
            } else {
                $renditionName = $fallbackRenditionName;
            }

            /* @var ImageRenditionInterface $rendition */
            foreach ($articleMedia->getRenditions() as $rendition) {
                if ($rendition->getName() === $renditionName) {
                    return $this->metaFactory->create($rendition);
                }

                if ($rendition->getName() === $fallbackRenditionName) {
                    $fallbackRendition = $rendition;
                }
            }

            if (null !== $fallbackRendition) {
                return $this->metaFactory->create($fallbackRendition);
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(string $type): bool
    {
        return in_array($type, ['rendition']);
    }
}
