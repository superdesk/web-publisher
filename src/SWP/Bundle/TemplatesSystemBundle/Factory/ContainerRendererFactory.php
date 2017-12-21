<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Factory;

use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

class ContainerRendererFactory implements ContainerRendererFactoryInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * Factory constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContainerInterface $containerEntity, \Twig_Environment $renderer = null, $debug = false, $cacheDir = null)
    {
        return new $this->className($containerEntity, $renderer, $debug, $cacheDir);
    }
}
