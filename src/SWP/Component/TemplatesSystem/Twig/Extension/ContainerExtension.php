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

use SWP\Component\TemplatesSystem\Twig\TokenParser\ContainerTokenParser;

class ContainerExtension extends \Twig_Extension
{
    protected $containerService;

    public function __construct($containerService)
    {
        $this->containerService = $containerService;
    }

    public function getContainerService()
    {
        return $this->containerService;
    }

    public function getTokenParsers()
    {
        return [
            new ContainerTokenParser(),
        ];
    }

    public function getName()
    {
        return 'swp_container';
    }
}
