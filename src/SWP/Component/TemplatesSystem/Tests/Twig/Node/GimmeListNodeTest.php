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

namespace SWP\Component\TemplatesSystem\Tests\Twig\Node;

use Doctrine\Common\Cache\ArrayCache;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Gimme\Loader\ChainLoader;
use SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension;
use SWP\Component\TemplatesSystem\Twig\Node\GimmeListNode;

class GimmeListNodeTest extends \Twig_Test_NodeTestCase
{
    /**
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null, $isPattern = false)
    {
        $env = $this->getEnvironment();
        $metaLoader = new ChainLoader();
        $context = new Context(new ArrayCache());
        $metaLoader->addLoader(new ArticleLoader(__DIR__, new MetaFactory($context)));
        $env->addExtension(new GimmeExtension($context, $metaLoader));

        $this->assertNodeCompilation($source, $node, $env);
    }

    public function testConstructor()
    {
        $variable = new \Twig_Node([new \Twig_Node_Expression_AssignName('article', 1)]);
        $collectionType = new \Twig_Node([new \Twig_Node_Expression_AssignName('articles', 1)]);
        $collectionFilters = new \Twig_Node_Expression_Filter(
            new \Twig_Node([$collectionType], [], 0),
            new \Twig_Node_Expression_Constant('start', 0),
            new \Twig_Node([new \Twig_Node_Expression_Constant(0, 0)]),
            0
        );
        $ifExpression = new \Twig_Node_Expression_Binary_Equal(new \Twig_Node_Expression_GetAttr(new \Twig_Node_Expression_Name('article', 0), new \Twig_Node_Expression_Constant('title', 0), null, null, 0),
            new \Twig_Node_Expression_Constant('New article', 0),
            0
        );

        $withParameters = new \Twig_Node_Expression_Array([], 1);
        $withoutParameters = new \Twig_Node_Expression_Array([], 1);
        $else = new \Twig_Node_Text('', 1);
        $body = new \Twig_Node_Text('', 1);
        $ignoreContext = new \Twig_Node_Expression_Array([], 1);

        $node = new GimmeListNode($variable, $collectionType, $collectionFilters, $withParameters, $withoutParameters, $ignoreContext, $ifExpression, $else, $body, 0, 'gimmelist');
        $this->assertEquals($variable, $node->getNode('variable'));
        $this->assertEquals($withParameters, $node->getNode('withParameters'));
        $this->assertEquals($withoutParameters, $node->getNode('withoutParameters'));

        $body = new \Twig_Node([$body, new \Twig_Node_ForLoop(0, 'gimmelist')]);
        $body = new \Twig_Node_If(new \Twig_Node([$ifExpression, $body]), null, 0, 'gimmelist');
        $this->assertEquals($body, $node->getNode('body'));
    }

    public function getTests()
    {
        $variable = new \Twig_Node([new \Twig_Node_Expression_AssignName('article', 1)]);
        $collectionType = new \Twig_Node([new \Twig_Node_Expression_AssignName('articles', 1)]);
        $collectionFilters = new \Twig_Node_Expression_Filter(
            new \Twig_Node([$collectionType], [], 0),
            new \Twig_Node_Expression_Constant('start', 0),
            new \Twig_Node([new \Twig_Node_Expression_Constant(0, 0)]),
            0
        );
        $collectionFiltersFull = new \Twig_Node_Expression_Filter(
                new \Twig_Node([new \Twig_Node_Expression_Filter(
                    new \Twig_Node([
                        new \Twig_Node([new \Twig_Node_Expression_Filter(
                            new \Twig_Node([$collectionType], [], 0),
                            new \Twig_Node_Expression_Constant('order', 0),
                            new \Twig_Node([new \Twig_Node_Expression_Array([new \Twig_Node_Expression_Constant('id', 0), new \Twig_Node_Expression_Constant('desc', 0)], 0)]),
                            0
                        ),
                        ], [], 0),
                    ], [], 0),
                    new \Twig_Node_Expression_Constant('limit', 0),
                    new \Twig_Node([new \Twig_Node_Expression_Constant(10, 0)]),
                    0
                ),
                ], [], 0),
            new \Twig_Node_Expression_Constant('start', 0),
            new \Twig_Node([new \Twig_Node_Expression_Constant(0, 0)]),
            0
        );
        $parameters = new \Twig_Node_Expression_Array([], 1);
        $withoutParameters = new \Twig_Node_Expression_Array([], 1);
        $ignoreContext = new \Twig_Node_Expression_Array([], 1);
        $ifExpression = new \Twig_Node_Expression_Binary_Equal(new \Twig_Node_Expression_GetAttr(new \Twig_Node_Expression_Name('article', 0), new \Twig_Node_Expression_Constant('title', 0), null, null, 0),
            new \Twig_Node_Expression_Constant('New article', 0),
            0
        );
        $else = new \Twig_Node_Text('', 1);
        $body = new \Twig_Node_Text('', 1);

        $node1 = new GimmeListNode($variable, $collectionType, null, null, null, null, null, null, $body, 0, 'gimmelist');
        $node2 = new GimmeListNode($variable, $collectionType, $collectionFilters, $parameters, null, null, $ifExpression, $else, $body, 0, 'gimmelist');
        $node3 = new GimmeListNode($variable, $collectionType, $collectionFiltersFull, null, null, null, null, null, $body, 0, 'gimmelist');
        $node4 = new GimmeListNode($variable, $collectionType, $collectionFiltersFull, $parameters, $withoutParameters, null, null, null, $body, 0, 'gimmelist');
        $node5 = new GimmeListNode($variable, $collectionType, $collectionFiltersFull, $parameters, $withoutParameters, null, $ifExpression, null, $body, 0, 'gimmelist');
        $node6 = new GimmeListNode($variable, $collectionType, $collectionFiltersFull, $parameters, $withoutParameters, $ignoreContext, $ifExpression, null, $body, 0, 'gimmelist');

        return [
            [$node1, <<<EOF
\$withParameters = [];
\$withoutParameters = [];
\$swpCollectionMetaLoader1 = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
\$context["articles"] = twig_ensure_traversable(\$swpCollectionMetaLoader1->load("articles", \$withParameters, \$withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));
\$context['_parent'] = (array) \$context;
\$context['loop'] = array(
  'parent' => \$context['_parent'],
  'index0' => 0,
  'index'  => 1,
  'first'  => true,
);
if (is_array(\$context["articles"]) || (is_object(\$context["articles"]) && \$context["articles"] instanceof Countable)) {
    \$length = count(\$context["articles"]);
    \$context['loop']['revindex0'] = \$length - 1;
    \$context['loop']['revindex'] = \$length;
    \$context['loop']['length'] = \$length;
    \$context['loop']['totalLength'] = \$length;
    \$context['loop']['last'] = 1 === \$length;
}
if(is_object(\$context["articles"]) && \$context["articles"] instanceof \SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection) {
    \$context['loop']['totalLength'] = \$context["articles"]->getTotalItemsCount();
}
foreach (\$context["articles"] as \$_key => \$context["article"]) {
    // line 1
    echo "";
    ++\$context['loop']['index0'];
    ++\$context['loop']['index'];
    \$context['loop']['first'] = false;
    if (isset(\$context['loop']['length'])) {
        --\$context['loop']['revindex0'];
        --\$context['loop']['revindex'];
        \$context['loop']['last'] = 0 === \$context['loop']['revindex0'];
    }
}
\$_parent = \$context['_parent'];
unset(\$context['article'], \$context['_iterated'], \$context['articles'], \$context['_parent'], \$context['loop']);
\$context = array_intersect_key(\$context, \$_parent) + \$_parent;
EOF
            ],
            [$node2, <<<EOF
\$context['_collection_type_filters'] = [];
\$context['articles'] = null;
\$context['_collection_type_filters'] = call_user_func_array(\$this->env->getFilter('start')->getCallable(), array(\$context, \$context["articles"], 0))['_collection_type_filters']; unset(\$context['articles']['_collection_type_filters']);
\$withParameters = array_merge(array(), \$context['_collection_type_filters']);
\$withoutParameters = [];
\$swpCollectionMetaLoader2 = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
\$context["articles"] = twig_ensure_traversable(\$swpCollectionMetaLoader2->load("articles", \$withParameters, \$withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));
\$context['_parent'] = (array) \$context;
\$context['_iterated'] = false;
\$context['loop'] = array(
  'parent' => \$context['_parent'],
  'index0' => 0,
  'index'  => 1,
  'first'  => true,
);
foreach (\$context["articles"] as \$_key => \$context["article"]) {
    if ((twig_get_attribute(\$this->env, \$this->getSourceContext(), (\$context["article"] ?? null), "title", array(), null) == "New article")) {
        // line 1
        echo "";
        \$context['_iterated'] = true;
        ++\$context['loop']['index0'];
        ++\$context['loop']['index'];
        \$context['loop']['first'] = false;
    }
}
if (!\$context['_iterated']) {
    echo "";
}
\$_parent = \$context['_parent'];
unset(\$context['article'], \$context['_iterated'], \$context['articles'], \$context['_parent'], \$context['loop']);
unset(\$context['_collection_type_filters']);
\$context = array_intersect_key(\$context, \$_parent) + \$_parent;
EOF
            ],
            [$node3, <<<EOF
\$context['_collection_type_filters'] = [];
\$context['articles'] = null;
\$context['_collection_type_filters'] = call_user_func_array(\$this->env->getFilter('start')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('limit')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('order')->getCallable(), array(\$context, \$context["articles"], array("id" => "desc"))), 10)), 0))['_collection_type_filters']; unset(\$context['articles']['_collection_type_filters']);
\$withParameters = \$context['_collection_type_filters'];
\$withoutParameters = [];
\$swpCollectionMetaLoader3 = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
\$context["articles"] = twig_ensure_traversable(\$swpCollectionMetaLoader3->load("articles", \$withParameters, \$withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));
\$context['_parent'] = (array) \$context;
\$context['loop'] = array(
  'parent' => \$context['_parent'],
  'index0' => 0,
  'index'  => 1,
  'first'  => true,
);
if (is_array(\$context["articles"]) || (is_object(\$context["articles"]) && \$context["articles"] instanceof Countable)) {
    \$length = count(\$context["articles"]);
    \$context['loop']['revindex0'] = \$length - 1;
    \$context['loop']['revindex'] = \$length;
    \$context['loop']['length'] = \$length;
    \$context['loop']['totalLength'] = \$length;
    \$context['loop']['last'] = 1 === \$length;
}
if(is_object(\$context["articles"]) && \$context["articles"] instanceof \SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection) {
    \$context['loop']['totalLength'] = \$context["articles"]->getTotalItemsCount();
}
foreach (\$context["articles"] as \$_key => \$context["article"]) {
    // line 1
    echo "";
    ++\$context['loop']['index0'];
    ++\$context['loop']['index'];
    \$context['loop']['first'] = false;
    if (isset(\$context['loop']['length'])) {
        --\$context['loop']['revindex0'];
        --\$context['loop']['revindex'];
        \$context['loop']['last'] = 0 === \$context['loop']['revindex0'];
    }
}
\$_parent = \$context['_parent'];
unset(\$context['article'], \$context['_iterated'], \$context['articles'], \$context['_parent'], \$context['loop']);
unset(\$context['_collection_type_filters']);
\$context = array_intersect_key(\$context, \$_parent) + \$_parent;
EOF
            ],
            [$node4, <<<EOF
\$context['_collection_type_filters'] = [];
\$context['articles'] = null;
\$context['_collection_type_filters'] = call_user_func_array(\$this->env->getFilter('start')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('limit')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('order')->getCallable(), array(\$context, \$context["articles"], array("id" => "desc"))), 10)), 0))['_collection_type_filters']; unset(\$context['articles']['_collection_type_filters']);
\$withParameters = array_merge(array(), \$context['_collection_type_filters']);
\$withoutParameters = array();
\$swpCollectionMetaLoader4 = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
\$context["articles"] = twig_ensure_traversable(\$swpCollectionMetaLoader4->load("articles", \$withParameters, \$withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));
\$context['_parent'] = (array) \$context;
\$context['loop'] = array(
  'parent' => \$context['_parent'],
  'index0' => 0,
  'index'  => 1,
  'first'  => true,
);
if (is_array(\$context["articles"]) || (is_object(\$context["articles"]) && \$context["articles"] instanceof Countable)) {
    \$length = count(\$context["articles"]);
    \$context['loop']['revindex0'] = \$length - 1;
    \$context['loop']['revindex'] = \$length;
    \$context['loop']['length'] = \$length;
    \$context['loop']['totalLength'] = \$length;
    \$context['loop']['last'] = 1 === \$length;
}
if(is_object(\$context["articles"]) && \$context["articles"] instanceof \SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection) {
    \$context['loop']['totalLength'] = \$context["articles"]->getTotalItemsCount();
}
foreach (\$context["articles"] as \$_key => \$context["article"]) {
    // line 1
    echo "";
    ++\$context['loop']['index0'];
    ++\$context['loop']['index'];
    \$context['loop']['first'] = false;
    if (isset(\$context['loop']['length'])) {
        --\$context['loop']['revindex0'];
        --\$context['loop']['revindex'];
        \$context['loop']['last'] = 0 === \$context['loop']['revindex0'];
    }
}
\$_parent = \$context['_parent'];
unset(\$context['article'], \$context['_iterated'], \$context['articles'], \$context['_parent'], \$context['loop']);
unset(\$context['_collection_type_filters']);
\$context = array_intersect_key(\$context, \$_parent) + \$_parent;
EOF
            ],
            [$node5, <<<EOF
\$context['_collection_type_filters'] = [];
\$context['articles'] = null;
\$context['_collection_type_filters'] = call_user_func_array(\$this->env->getFilter('start')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('limit')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('order')->getCallable(), array(\$context, \$context["articles"], array("id" => "desc"))), 10)), 0))['_collection_type_filters']; unset(\$context['articles']['_collection_type_filters']);
\$withParameters = array_merge(array(), \$context['_collection_type_filters']);
\$withoutParameters = array();
\$swpCollectionMetaLoader5 = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
\$context["articles"] = twig_ensure_traversable(\$swpCollectionMetaLoader5->load("articles", \$withParameters, \$withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));
\$context['_parent'] = (array) \$context;
\$context['loop'] = array(
  'parent' => \$context['_parent'],
  'index0' => 0,
  'index'  => 1,
  'first'  => true,
);
foreach (\$context["articles"] as \$_key => \$context["article"]) {
    if ((twig_get_attribute(\$this->env, \$this->getSourceContext(), (\$context["article"] ?? null), "title", array(), null) == "New article")) {
        // line 1
        echo "";
        ++\$context['loop']['index0'];
        ++\$context['loop']['index'];
        \$context['loop']['first'] = false;
    }
}
\$_parent = \$context['_parent'];
unset(\$context['article'], \$context['_iterated'], \$context['articles'], \$context['_parent'], \$context['loop']);
unset(\$context['_collection_type_filters']);
\$context = array_intersect_key(\$context, \$_parent) + \$_parent;
EOF
            ],
            [$node6, <<<EOF
\$context['_collection_type_filters'] = [];
\$context['articles'] = null;
\$context['_collection_type_filters'] = call_user_func_array(\$this->env->getFilter('start')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('limit')->getCallable(), array(\$context, call_user_func_array(\$this->env->getFilter('order')->getCallable(), array(\$context, \$context["articles"], array("id" => "desc"))), 10)), 0))['_collection_type_filters']; unset(\$context['articles']['_collection_type_filters']);
\$withParameters = array_merge(array(), \$context['_collection_type_filters']);
\$withoutParameters = array();
\$swpCollectionMetaLoader6 = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
\$swpContext6GimmeList = \$this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getContext();
\$swpIgnoreContext6GimmeList = \$swpContext6GimmeList->temporaryUnset(array());
\$context["articles"] = twig_ensure_traversable(\$swpCollectionMetaLoader6->load("articles", \$withParameters, \$withoutParameters, \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::COLLECTION));
\$context['_parent'] = (array) \$context;
\$context['loop'] = array(
  'parent' => \$context['_parent'],
  'index0' => 0,
  'index'  => 1,
  'first'  => true,
);
foreach (\$context["articles"] as \$_key => \$context["article"]) {
    if ((twig_get_attribute(\$this->env, \$this->getSourceContext(), (\$context["article"] ?? null), "title", array(), null) == "New article")) {
        // line 1
        echo "";
        ++\$context['loop']['index0'];
        ++\$context['loop']['index'];
        \$context['loop']['first'] = false;
    }
}
\$swpContext6GimmeList->restoreTemporaryUnset(\$swpIgnoreContext6GimmeList);
\$_parent = \$context['_parent'];
unset(\$context['article'], \$context['_iterated'], \$context['articles'], \$context['_parent'], \$context['loop']);
unset(\$context['_collection_type_filters']);
\$context = array_intersect_key(\$context, \$_parent) + \$_parent;
EOF
            ],
        ];
    }

    protected function tearDown()
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }
}
