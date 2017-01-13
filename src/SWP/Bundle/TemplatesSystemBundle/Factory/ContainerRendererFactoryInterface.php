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

interface ContainerRendererFactoryInterface
{
    /**
     * @param ContainerInterface     $containerEntity
     * @param \Twig_Environment|null $renderer
     * @param bool                   $debug
     * @param null                   $cacheDir
     *
     * @return mixed
     */
    public function create(
        ContainerInterface $containerEntity,
        \Twig_Environment $renderer = null,
        $debug = false,
        $cacheDir = null
    );
}
