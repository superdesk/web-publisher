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
 * Gimme twig node.
 */
class GimmeNode extends \Twig_Node
{
    private static $count = 1;

    /**
     * @param \Twig_Node_Expression $annotation
     * @param \Twig_Node_Expression $parameters
     * @param \Twig_NodeInterface   $body
     * @param int                   $lineno
     * @param string                $tag
     */
    public function __construct(\Twig_Node $annotation, \Twig_Node_Expression $parameters = null, \Twig_NodeInterface $body, $lineno, $tag = null)
    {
        parent::__construct(['parameters' => $parameters, 'body' => $body, 'annotation' => $annotation], [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $i = self::$count++;

        $compiler
            ->addDebugInfo($this)
            ->write('$swpMetaLoader'.$i." = \$this->env->getExtension('swp_gimme')->getLoader();\n")
            ->write('')->subcompile($this->getNode('annotation'))->raw(' = $swpMetaLoader'.$i.'->load("')->raw($this->getNode('annotation')->getNode(0)->getAttribute('name'))->raw('", ');
        if (!is_null($this->getNode('parameters'))) {
            $compiler->subcompile($this->getNode('parameters'));
        } else {
            $compiler->raw('null');
        }
        $compiler->raw(");\n")
            ->write('if (')->subcompile($this->getNode('annotation'))->raw(" !== false) {\n")
            ->indent()
                ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("}\n")
            ->write('unset(')->subcompile($this->getNode('annotation'))->raw(');');
    }
}
