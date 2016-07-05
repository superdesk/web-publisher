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
namespace SWP\Component\TemplatesSystem\Twig\Node;

/**
 * Container twig node.
 */
class ContainerNode extends \Twig_Node
{
    private static $count = 1;

    /**
     * @param \Twig_Node_Expression $name
     * @param \Twig_Node_Expression $parameters
     * @param \Twig_NodeInterface   $body
     * @param int                   $lineno
     * @param string                $tag
     */
    public function __construct(\Twig_Node $name, \Twig_Node_Expression $parameters = null, \Twig_NodeInterface $body, $lineno, $tag = null)
    {
        parent::__construct(['name' => $name, 'parameters' => $parameters, 'body' => $body], [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("\$containerService = \$this->env->getExtension('swp_container')->getContainerService();\n")
            ->write('$container = $containerService->getContainer(')->subcompile($this->getNode('name'))->raw(', ');
        if (!is_null($this->getNode('parameters'))) {
            $compiler->subcompile($this->getNode('parameters'));
        } else {
            $compiler->raw('array()');
        }
        $compiler->raw(");\n")
            ->write("if (\$container->isVisible()) {\n")
            ->indent()
                ->write("echo \$container->renderOpenTag();\n")
                ->write("if (\$container->hasWidgets()) {\n")
                ->indent()
                    ->write("echo \$container->renderWidgets();\n")
                ->outdent()
                ->write("} else {\n")
                ->indent()
                    ->subcompile($this->getNode('body'))
                ->outdent()
                ->write("}\n")
                ->write("echo \$container->renderCloseTag();\n")
            ->outdent()
            ->write("}\n")
            ->write("unset(\$container);unset(\$containerService);\n");
    }
}
