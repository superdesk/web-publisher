<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Twig;

class StringyExtension extends \Twig_Extension
{
    const EXCLUDE_FUNCTIONS = ['__construct', '__toString', 'create'];

    protected $environment;

    protected $functions = [];

    protected $filters = [];

    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        $this->lazyInit();

        return $this->filters;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        $this->lazyInit();

        return $this->functions;
    }

    /**
     * Initializes arrays of filters and functions
     */
    private function lazyInit()
    {
        $stringyClass = new \ReflectionClass('Stringy\Stringy');
        $methods = $stringyClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $names = array_map(function($value){
            return $value->getName();
        }, $methods);

        foreach ($names as $name) {
            if (in_array($name, self::EXCLUDE_FUNCTIONS)) {
                continue;
            }

            if ($this->environment->getFilter($name)) {
                continue;
            }

            $method = $stringyClass->getMethod($name);
            $doc = $method->getDocComment();
            if (strpos($doc, '@return bool')) {
                $this->functions[$name] = new \Twig_SimpleFunction($name, array('Stringy\StaticStringy', $name));
            }

            else {
                $this->filters[$name] = new \Twig_SimpleFilter($name, array('Stringy\StaticStringy', $name));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'stringy_extension';
    }
}
