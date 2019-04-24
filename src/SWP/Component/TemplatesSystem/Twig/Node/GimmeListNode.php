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

use Twig\Node\ForLoopNode;
use Twig\Node\IfNode;

/**
 * Gimme twig node.
 */
class GimmeListNode extends \Twig\Node\Node
{
    protected static $count = 1;

    protected $loop;

    /**
     * GimmeListNode constructor.
     *
     * @param \Twig\Node\Node                        $variable
     * @param \Twig\Node\Node                        $collectionType
     * @param \Twig\Node\Node|null $collectionFilters
     * @param \Twig\Node\Node|null        $withParameters
     * @param \Twig\Node\Node|null        $withoutParameters
     * @param \Twig\Node\Node|null        $ignoreContext
     * @param \Twig\Node\Node|null        $ifExpression
     * @param \Twig\Node\Node|null                   $else
     * @param \Twig\Node\Node                        $body
     * @param int                               $lineno
     * @param null                              $tag
     */
    public function __construct(
        \Twig\Node\Node $variable,
        \Twig\Node\Node $collectionType,
        \Twig\Node\Expression\FilterExpression $collectionFilters = null,
        \Twig\Node\Expression\AbstractExpression $withParameters = null,
        \Twig\Node\Expression\AbstractExpression $withoutParameters = null,
        \Twig\Node\Expression\AbstractExpression $ignoreContext = null,
        \Twig\Node\Expression\AbstractExpression $ifExpression = null,
        \Twig\Node\Node $else = null,
        \Twig\Node\Node $body,
        $lineno,
        $tag = null
    ) {
        $body = new \Twig\Node\Node([$body, $this->loop = new ForLoopNode($lineno, $tag)]);

        if (null !== $ifExpression) {
            $body = new IfNode(new \Twig\Node\Node([$ifExpression, $body]), null, $lineno, $tag);
        }

        $nodes = [
            'variable' => $variable,
            'collectionType' => $collectionType,
            'body' => $body,
        ];

        if (!is_null($withParameters)) {
            $nodes['withParameters'] = $withParameters;
        }

        if (!is_null($withoutParameters)) {
            $nodes['withoutParameters'] = $withoutParameters;
        }

        if (!is_null($ignoreContext)) {
            $nodes['ignoreContext'] = $ignoreContext;
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
    public function compile(\Twig\Compiler $compiler)
    {
        $i = self::$count++;

        $collectionTypeName = $this->getNode('collectionType')->getNode(0)->getAttribute('name');

        $compiler
            ->addDebugInfo($this);

        if ($this->hasNode('collectionFilters')) {
            $compiler->write("\$context['_collection_type_filters'] = [];\n");
            $compiler->write("\$context['".$collectionTypeName."'] = null;\n");
            $compiler->write("\$context['_collection_type_filters'] = ")->subcompile($this->getNode('collectionFilters'))->raw("['_collection_type_filters']; unset(\$context['".$collectionTypeName."']['_collection_type_filters']);\n");

            if ($this->hasNode('withParameters')) {
                $compiler->write('$withParameters = array_merge(')->subcompile($this->getNode('withParameters'))->raw(", \$context['_collection_type_filters']);\n");
            } else {
                $compiler->write("\$withParameters = \$context['_collection_type_filters'];\n");
            }
        } else {
            if ($this->hasNode('withParameters')) {
                $compiler->raw('$withParameters = ')->subcompile($this->getNode('withParameters'))->raw(";\n");
            } else {
                $compiler->raw("\$withParameters = [];\n");
            }
        }

        if ($this->hasNode('withoutParameters')) {
            $compiler->raw('$withoutParameters = ')->subcompile($this->getNode('withoutParameters'))->raw(";\n");
        } else {
            $compiler->raw("\$withoutParameters = [];\n");
        }

        $compiler->write('$swpCollectionMetaLoader'.$i." = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();\n");
        if ($this->hasNode('ignoreContext')) {
            $compiler->write('$swpContext'.$i."GimmeList = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getContext();\n");
            $compiler->write('$swpIgnoreContext'.$i.'GimmeList = $swpContext'.$i.'GimmeList->temporaryUnset(')->subcompile($this->getNode('ignoreContext'))->raw(");\n");
        }
        $compiler->write('')->subcompile($this->getNode('collectionType'))->raw(' = twig_ensure_traversable($swpCollectionMetaLoader'.$i.'->load("')->raw($collectionTypeName)->raw('", ');
        $compiler->raw('$withParameters, $withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));')->raw("\n");

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

            if (!$this->getAttribute('ifexpr')) {
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

        if ($this->hasNode('ignoreContext')) {
            $compiler->write('$swpContext'.$i.'GimmeList->restoreTemporaryUnset($swpIgnoreContext'.$i."GimmeList);\n");
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
