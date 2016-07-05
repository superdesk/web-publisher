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
namespace SWP\Component\TemplatesSystem\Tests\Twig\Node;

use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Gimme\Loader\ChainLoader;
use SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension;
use SWP\Component\TemplatesSystem\Twig\Node\GimmeNode;

class GimmeNodeTest extends \Twig_Test_NodeTestCase
{
    public function testConstructor()
    {
        $annotation = new \Twig_Node([new \Twig_Node_Expression_AssignName('article', 1)]);
        $parameters = new \Twig_Node_Expression_Array([], 1);
        $body = new \Twig_Node_Text('', 1);
        $node = new GimmeNode($annotation, $parameters, $body, 1, 'gimme');
        $this->assertEquals($annotation, $node->getNode('annotation'));
        $this->assertEquals($parameters, $node->getNode('parameters'));
        $this->assertEquals($body, $node->getNode('body'));
    }

    public function getTests()
    {
        $annotation1 = new \Twig_Node([new \Twig_Node_Expression_AssignName('article', 1)]);
        $parameters1 = new \Twig_Node_Expression_Array([], 1);
        $body1 = new \Twig_Node_Text('Test body', 1);
        $node1 = new GimmeNode($annotation1, $parameters1, $body1, 1, 'gimme');

        $annotation2 = new \Twig_Node([new \Twig_Node_Expression_AssignName('article', 2)]);
        $body2 = new \Twig_Node_Text('Test body', 2);
        $node2 = new GimmeNode($annotation2, null, $body2, 2, 'gimme');

        $annotation3 = new \Twig_Node([new \Twig_Node_Expression_AssignName('article', 3)]);
        $parameters3 = new \Twig_Node_Expression_Array([new \Twig_Node_Expression_Constant('foo', 1), new \Twig_Node_Expression_Constant(true, 1)], 1);
        $body3 = new \Twig_Node_Text('Test body', 3);
        $node3 = new GimmeNode($annotation3, $parameters3, $body3, 3, 'gimme');

        return [
            [$node1, <<<'EOF'
// line 1
$swpMetaLoader3 = $this->env->getExtension('swp_gimme')->getLoader();
$context["article"] = $swpMetaLoader3->load("article", array());
if ($context["article"] !== false) {
    echo "Test body";
}
unset($context["article"]);
EOF
            ],
            [$node2, <<<'EOF'
// line 2
$swpMetaLoader4 = $this->env->getExtension('swp_gimme')->getLoader();
$context["article"] = $swpMetaLoader4->load("article", null);
if ($context["article"] !== false) {
    echo "Test body";
}
unset($context["article"]);
EOF
            ],
            [$node3, <<<'EOF'
// line 3
$swpMetaLoader5 = $this->env->getExtension('swp_gimme')->getLoader();
$context["article"] = $swpMetaLoader5->load("article", array("foo" => true));
if ($context["article"] !== false) {
    echo "Test body";
}
unset($context["article"]);
EOF
            ],
        ];
    }

    public function testTemplateString()
    {
        $loader = new \Twig_Loader_Array([
            'clear_gimme'           => '{% gimme article %}{{ article.title }}{% endgimme %}',
            'gimme_with_parameters' => '{% gimme article with {id: 1} %}{{ article.title }}{% endgimme %}',
        ]);
        $metaLoader = new ChainLoader();
        $metaLoader->addLoader(new ArticleLoader(__DIR__));
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new GimmeExtension(new Context(), $metaLoader));

        $this->assertEquals($twig->render('clear_gimme'), 'New article');
        $this->assertEquals($twig->render('gimme_with_parameters'), 'New article');
    }

    public function testBrokenTemplate()
    {
        $loader = new \Twig_Loader_Array([
            'error_gimme' => '{% gimme article {id: 1} %}{{ article.title }}{% endgimme %}',
        ]);
        $metaLoader = new ChainLoader();
        $metaLoader->addLoader(new ArticleLoader(__DIR__));
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new GimmeExtension(new Context(), $metaLoader));

        $this->setExpectedException('\Twig_Error_Syntax');
        $twig->render('error_gimme');
    }
}
