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

use SWP\Component\TemplatesSystem\Twig\TokenParser\ContainerTokenParser;
use Twig\Extension\AbstractExtension;

/**
 * @deprecated since 2.0, will be removed in 3.0
 */
class ContainerExtension extends AbstractExtension
{
    /**
     * @return array|\Twig_TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return [
            new ContainerTokenParser(),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }
}
