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

/**
 * @deprecated since 2.0, will be removed in 3.0
 * Container twig node.
 */
class ContainerNode extends \Twig_Node
{
    /**
     * ContainerNode constructor.
     *
     * @param \Twig_Node                 $name
     * @param \Twig_Node_Expression|null $parameters
     * @param \Twig_Node                 $body
     * @param string|null                $lineno
     * @param null                       $tag
     */
    public function __construct(\Twig_Node $name, \Twig_Node_Expression $parameters = null, \Twig_Node $body, $lineno, $tag = null)
    {
        $nodes = [
            'name' => $name,
            'body' => $body,
        ];

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("echo \"<!-- @deprecated: Container nodes are deprecated from 2.0, will be removed in 3.0 -->\"; \n");
    }
}
