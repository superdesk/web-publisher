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

namespace SWP\Component\TemplatesSystem\Twig\Extension;

use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Twig\TokenParser\GimmeListTokenParser;
use SWP\Component\TemplatesSystem\Twig\TokenParser\GimmeTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class GimmeExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var Context
     */
    protected $context;

    /**
     * GimmeExtension constructor.
     *
     * @param Context         $context
     * @param LoaderInterface $loader
     */
    public function __construct(Context $context, LoaderInterface $loader)
    {
        $this->context = $context;
        $this->loader = $loader;
    }

    /**
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return [
            new GimmeTokenParser(),
            new GimmeListTokenParser(),
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('start', function ($node, $value) {
                $node['_collection_type_filters']['start'] = $value;

                return $node;
            }, ['needs_context' => false]),
            new TwigFilter('limit', function ($node, $value) {
                $node['_collection_type_filters']['limit'] = $value;

                return $node;
            }, ['needs_context' => false]),
            new TwigFilter('order', function ($node, $value1, $value2) {
                $node['_collection_type_filters']['order'][] = [$value1, $value2];

                return $node;
            }, ['needs_context' => false]),
            new TwigFilter('dateRange', function ($node, $value1, $value2) {
                $node['_collection_type_filters']['date_range'] = [$value1, $value2];

                return $node;
            }, ['needs_context' => false]),
        ];
    }

    /**
     * @return array
     */
    public function getGlobals(): array
    {
        return ['gimme' => $this->context];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }
}
