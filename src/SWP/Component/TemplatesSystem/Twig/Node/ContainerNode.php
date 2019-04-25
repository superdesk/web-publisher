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

namespace SWP\Component\TemplatesSystem\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * @deprecated since 2.0, will be removed in 3.0
 * Container twig node.
 */
class ContainerNode extends Node
{
    public function __construct(Node $name, \Twig\Node\Expression\AbstractExpression $parameters = null, Node $body, $lineno, $tag = null)
    {
        $nodes = [
            'name' => $name,
            'body' => $body,
        ];

        if (!\is_null($parameters)) {
            $nodes['parameters'] = $parameters;
        }

        parent::__construct($nodes, [], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("echo \"<!-- @deprecated: Container nodes are deprecated from 2.0, will be removed in 3.0 -->\"; \n");
    }
}
