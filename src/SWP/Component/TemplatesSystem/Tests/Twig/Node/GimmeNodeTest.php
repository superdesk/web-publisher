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
use SWP\Component\TemplatesSystem\Twig\Node\GimmeNode;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Node\Expression\ArrayExpression;
use Twig\Test\NodeTestCase;
use Twig\Loader\ArrayLoader;
use Twig\Environment;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\AssignNameExpression;

class GimmeNodeTest extends NodeTestCase
{
    public function testConstructor()
    {
        $annotation = new Node([new AssignNameExpression('article', 1)]);
        $parameters = new ArrayExpression([], 1);
        $body = new TextNode('', 1);
        $node = new GimmeNode($annotation, $parameters, null, $body, 1, 'gimme');
        $this->assertEquals($annotation, $node->getNode('annotation'));
        $this->assertEquals($parameters, $node->getNode('parameters'));
        $this->assertEquals($body, $node->getNode('body'));
    }

    public function getTests()
    {
        $annotation1 = new Node([new AssignNameExpression('article', 1)]);
        $parameters1 = new ArrayExpression([], 1);
        $body1 = new TextNode('Test body', 1);
        $node1 = new GimmeNode($annotation1, $parameters1, null, $body1, 1, 'gimme');

        $annotation2 = new Node([new AssignNameExpression('article', 2)]);
        $body2 = new TextNode('Test body', 2);
        $node2 = new GimmeNode($annotation2, null, null, $body2, 2, 'gimme');

        $annotation3 = new Node([new AssignNameExpression('article', 3)]);
        $parameters3 = new ArrayExpression([new ConstantExpression('foo', 1), new ConstantExpression(true, 1)], 1);
        $body3 = new TextNode('Test body', 3);
        $node3 = new GimmeNode($annotation3, $parameters3, null, $body3, 3, 'gimme');

        $annotation4 = new Node([new AssignNameExpression('article', 3)]);
        $parameters4 = new ArrayExpression([new ConstantExpression('foo', 1), new ConstantExpression(true, 1)], 1);
        $ignoreContext4 = new ArrayExpression([], 1);
        $body4 = new TextNode('Test body', 4);
        $node4 = new GimmeNode($annotation4, $parameters4, $ignoreContext4, $body4, 4, 'gimme');

        return [
            [$node1, <<<'EOF'
// line 1
$swpMetaLoader3 = $this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
$context["article"] = $swpMetaLoader3->load("article", []);
if ($context["article"] !== false) {
    echo "Test body";
}
unset($context["article"]);
EOF
            ],
            [$node2, <<<'EOF'
// line 2
$swpMetaLoader4 = $this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
$context["article"] = $swpMetaLoader4->load("article", null);
if ($context["article"] !== false) {
    echo "Test body";
}
unset($context["article"]);
EOF
            ],
            [$node3, <<<'EOF'
// line 3
$swpMetaLoader5 = $this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
$context["article"] = $swpMetaLoader5->load("article", ["foo" => true]);
if ($context["article"] !== false) {
    echo "Test body";
}
unset($context["article"]);
EOF
            ],
            [$node4, <<<'EOF'
// line 4
$swpMetaLoader6 = $this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getLoader();
$swpContext6Gimme = $this->env->getExtension('SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension')->getContext();
$swpIgnoreContext6Gimme = $swpContext6Gimme->temporaryUnset([]);
$context["article"] = $swpMetaLoader6->load("article", ["foo" => true]);
if ($context["article"] !== false) {
    echo "Test body";
}
$swpContext6Gimme->restoreTemporaryUnset($swpIgnoreContext6Gimme);
unset($context["article"]);
EOF
            ],
        ];
    }

    public function testTemplateString()
    {
        $loader = new ArrayLoader([
            'clear_gimme' => '{% gimme article %}{{ article.title }}{% endgimme %}',
            'gimme_with_parameters' => '{% gimme article with {id: 1} %}{{ article.title }}{% endgimme %}',
        ]);
        $metaLoader = new ChainLoader();
        $context = new Context(new EventDispatcher(), new ArrayCache());
        $metaLoader->addLoader(new ArticleLoader(__DIR__, new MetaFactory($context)));
        $twig = new Environment($loader);
        $twig->addExtension(new GimmeExtension($context, $metaLoader));

        $this->assertEquals($twig->render('clear_gimme'), 'New article');
        $this->assertEquals($twig->render('gimme_with_parameters'), 'New article');
    }

    public function testBrokenTemplate()
    {
        $loader = new ArrayLoader([
            'error_gimme' => '{% gimme article {id: 1} %}{{ article.title }}{% endgimme %}',
        ]);
        $metaLoader = new ChainLoader();
        $context = new Context(new EventDispatcher(), new ArrayCache());
        $metaLoader->addLoader(new ArticleLoader(__DIR__, new MetaFactory($context)));
        $twig = new Environment($loader);
        $twig->addExtension(new GimmeExtension($context, $metaLoader));

        $this->expectException(\Twig\Error\SyntaxError::class);
        $twig->render('error_gimme');
    }

    protected function tearDown(): void
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
