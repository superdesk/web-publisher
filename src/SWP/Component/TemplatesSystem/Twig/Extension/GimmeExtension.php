<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Twig\Extension;

use SWP\Component\TemplatesSystem\Twig\TokenParser\GimmeListTokenParser;
use SWP\Component\TemplatesSystem\Twig\TokenParser\GimmeTokenParser;

class GimmeExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    protected $loader;

    protected $context;

    public function __construct($context, $loader)
    {
        $this->context = $context;
        $this->loader = $loader;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getTokenParsers()
    {
        return [
            new GimmeTokenParser(),
            new GimmeListTokenParser(),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('start', function ($context, $node, $value) {
                $node['_collection_type_filters']['start'] = $value;

                return $node;
            }, ['needs_context' => true]),
            new \Twig_SimpleFilter('limit', function ($context, $node, $value) {
                $node['_collection_type_filters']['limit'] = $value;

                return $node;
            }, ['needs_context' => true]),
            new \Twig_SimpleFilter('order', function ($context, $node, $value1, $value2) {
                $node['_collection_type_filters']['order'] = [$value1, $value2];

                return $node;
            }, ['needs_context' => true]),
        ];
    }

    public function getGlobals()
    {
        return ['gimme' => $this->context];
    }

    public function getName()
    {
        return 'swp_gimme';
    }
}
