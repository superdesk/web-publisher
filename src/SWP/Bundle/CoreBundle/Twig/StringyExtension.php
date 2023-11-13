<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Environment as TwigEnvironment;

/**
 * Class StringyExtension.
 */
class StringyExtension extends AbstractExtension
{
    const EXCLUDE_FUNCTIONS = ['__construct', 'create'];

    protected $initialized = false;

    /**
     * @var TwigEnvironment
     */
    protected $environment;

    /**
     * @var array
     */
    protected $functions = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * StringyExtension constructor.
     *
     * @param TwigEnvironment $environment
     */
    public function __construct(TwigEnvironment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $this->lazyInit();

        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $this->lazyInit();

        return $this->functions;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * Initializes arrays of filters and functions.
     */
    private function lazyInit()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;
        $stringyClass = new \ReflectionClass('Stringy\Stringy');
        $methods = $stringyClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $names = array_map(function ($value) {
            return $value->getName();
        }, $methods);

        foreach ($names as $name) {
            if (in_array($name, self::EXCLUDE_FUNCTIONS)) {
                continue;
            }

            $method = $stringyClass->getMethod($name);
            // Get the return type from the doc comment
            $doc = $method->getDocComment();
            if (strpos($doc, '@return bool')) {
                // Don't add functions which have the same name as any already in the environment
                if (array_key_exists($name, $this->environment->getFunctions())) {
                    continue;
                }
                $this->functions[$name] = new TwigFunction($name, function () use ($name) {
                    return call_user_func_array(['Stringy\StaticStringy', $name], func_get_args());
                });
            } else {
                // Don't add filters which have the same name as any already in the environment
                if (array_key_exists($name, $this->environment->getFilters())) {
                    continue;
                }
                $this->filters[$name] = new TwigFilter($name, function () use ($name) {
                    return (string) call_user_func_array(['Stringy\StaticStringy', $name], func_get_args());
                });
            }
        }
    }
}
