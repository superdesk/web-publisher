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
 * Gimme twig node.
 */
class GimmeListNode extends \Twig_Node
{
    protected static $count = 1;

    protected $loop;

    /**
     * GimmeListNode constructor.
     *
     * @param \Twig_Node                        $variable
     * @param \Twig_Node                        $collectionType
     * @param \Twig_Node_Expression_Filter|null $collectionFilters
     * @param \Twig_Node_Expression|null        $parameters
     * @param \Twig_Node_Expression|null        $ifExpression
     * @param \Twig_NodeInterface|null          $else
     * @param \Twig_NodeInterface               $body
     * @param                                   $lineno
     * @param null                              $tag
     */
    public function __construct(\Twig_Node $variable, \Twig_Node $collectionType, \Twig_Node_Expression_Filter $collectionFilters = null, \Twig_Node_Expression $parameters = null, \Twig_Node_Expression $ifExpression = null, \Twig_NodeInterface $else = null, \Twig_NodeInterface $body, $lineno, $tag = null)
    {
        $body = new \Twig_Node([$body, $this->loop = new \Twig_Node_ForLoop($lineno, $tag)]);

        if (null !== $ifExpression) {
            $body = new \Twig_Node_If(new \Twig_Node([$ifExpression, $body]), null, $lineno, $tag);
        }

        $nodes = [
            'variable' => $variable,
            'collectionType' => $collectionType,
            'body' => $body,
        ];

        if (!is_null($parameters)) {
            $nodes['parameters'] = $parameters;
        }

        if (!is_null($collectionFilters)) {
            $nodes['collectionFilters'] = $collectionFilters;
        }

        if (!is_null($ifExpression)) {
            $nodes['ifExpression'] = $ifExpression;
        }

        if (!is_null($else)) {
            $nodes['else'] = $else;
        }

        parent::__construct($nodes, ['with_loop' => true, 'ifexpr' => null !== $ifExpression], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $i = self::$count++;

        $collectionTypeName = $this->getNode('collectionType')->getNode(0)->getAttribute('name');

        $compiler
            ->addDebugInfo($this);

        if ($this->hasNode('collectionFilters')) {
            $compiler->write("\$context['_collection_type_filters'] = [];\n");
            $compiler->write("\$context['".$collectionTypeName."'] = null;\n");
            $compiler->write("\$context['_collection_type_filters'] = ")->subcompile($this->getNode('collectionFilters'))->raw("['_collection_type_filters']; unset(\$context['".$collectionTypeName."']['_collection_type_filters']);\n");

            if ($this->hasNode('parameters')) {
                $compiler->write('$parameters = array_merge(')->subcompile($this->getNode('parameters'))->raw(", \$context['_collection_type_filters']);\n");
            } else {
                $compiler->write("\$parameters = \$context['_collection_type_filters'];\n");
            }
        } else {
            if ($this->hasNode('parameters')) {
                $compiler->raw('$parameters = ')->subcompile($this->getNode('parameters'))->raw(";\n");
            } else {
                $compiler->raw("\$parameters = [];\n");
            }
        }

        $compiler->write('$swpCollectionMetaLoader'.$i." = \$this->env->getExtension('swp_gimme')->getLoader();\n")
            ->write('')->subcompile($this->getNode('collectionType'))->raw(' = twig_ensure_traversable($swpCollectionMetaLoader'.$i.'->load("')->raw($collectionTypeName)->raw('", ');
        $compiler->raw('$parameters');
        $compiler->raw(", \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));\n");

        // the (array) cast bypasses a PHP 5.2.6 bug
        $compiler->write("\$context['_parent'] = (array) \$context;\n");

        if ($this->hasNode('else')) {
            $compiler->write("\$context['_iterated'] = false;\n");
        }

        if ($this->getAttribute('with_loop')) {
            $compiler
                ->write("\$context['loop'] = array(\n")
                ->write("  'parent' => \$context['_parent'],\n")
                ->write("  'index0' => 0,\n")
                ->write("  'index'  => 1,\n")
                ->write("  'first'  => true,\n")
                ->write(");\n");

            if (!$this->getAttribute('ifexpr') && $this->getNode('collectionType')) {
                $compiler
                    ->write('if (is_array(')->subcompile($this->getNode('collectionType'))->raw(') || (is_object(')->subcompile($this->getNode('collectionType'))->raw(') && ')->subcompile($this->getNode('collectionType'))->raw(" instanceof Countable)) {\n")
                    ->indent()
                    ->write('$length = count(')->subcompile($this->getNode('collectionType'))->raw(");\n")
                    ->write("\$context['loop']['revindex0'] = \$length - 1;\n")
                    ->write("\$context['loop']['revindex'] = \$length;\n")
                    ->write("\$context['loop']['length'] = \$length;\n")
                    ->write("\$context['loop']['totalLength'] = \$length;\n")
                    ->write("\$context['loop']['last'] = 1 === \$length;\n")
                    ->outdent()
                    ->write("}\n");

                $compiler
                    ->write('if(is_object(')->subcompile($this->getNode('collectionType'))->raw(') && ')->subcompile($this->getNode('collectionType'))->raw(" instanceof \SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection) {\n")
                    ->indent()
                    ->write('$context[\'loop\'][\'totalLength\'] = ')->subcompile($this->getNode('collectionType'))->raw("->getTotalItemsCount();\n")
                    ->outdent()
                    ->write("}\n");
            }
        }

        $this->loop->setAttribute('else', $this->hasNode('else'));
        $this->loop->setAttribute('with_loop', $this->getAttribute('with_loop'));
        $this->loop->setAttribute('ifexpr', $this->getAttribute('ifexpr'));

        if (null !== $this->getNode('collectionType')) {
            $compiler
                ->write('foreach (')
                ->subcompile($this->getNode('collectionType'))
                ->raw(' as $_key')
                ->raw(' => ')
                ->subcompile($this->getNode('variable'))
                ->raw(") {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->outdent()
                ->write("}\n");
        }

        if ($this->hasNode('else')) {
            $compiler
                ->write("if (!\$context['_iterated']) {\n")
                ->indent()
                ->subcompile($this->getNode('else'))
                ->outdent()
                ->write("}\n");
        }

        $compiler->write("\$_parent = \$context['_parent'];\n");

        // remove some "private" loop variables (needed for nested loops)
        $compiler->write('unset($context[\''.$this->getNode('variable')->getNode(0)->getAttribute('name').'\'], $context[\'_iterated\'], $context[\''.$collectionTypeName.'\'], $context[\'_parent\'], $context[\'loop\']);'."\n");

        if ($this->hasNode('collectionFilters')) {
            $compiler->write("unset(\$context['_collection_type_filters']);\n");
        }

        // keep the values set in the inner context for variables defined in the outer context
        $compiler->write("\$context = array_intersect_key(\$context, \$_parent) + \$_parent;\n");
    }
}
