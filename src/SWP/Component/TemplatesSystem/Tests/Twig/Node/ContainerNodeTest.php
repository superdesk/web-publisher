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

use SWP\Component\TemplatesSystem\Twig\Node\ContainerNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

class ContainerNodeTest extends NodeTestCase
{
    public function testConstructor()
    {
        $name = new Node([new ConstantExpression('container_name', 1)]);
        $parameters = new ArrayExpression([], 1);
        $body = new TextNode('', 1);
        $node = new ContainerNode($name, $parameters, $body, 1, 'gimme');
        $this->assertEquals($name, $node->getNode('name'));
        $this->assertEquals($parameters, $node->getNode('parameters'));
        $this->assertEquals($body, $node->getNode('body'));
    }

    public function getTests()
    {
        $name1 = new Node([new ConstantExpression('container_name', 1)]);
        $parameters1 = new ArrayExpression([], 1);
        $body1 = new TextNode('Test body', 1);
        $node1 = new ContainerNode($name1, $parameters1, $body1, 1, 'gimme');

        $name2 = new Node([new ConstantExpression('container_name', 2)]);
        $body2 = new TextNode('Test body', 2);
        $node2 = new ContainerNode($name2, null, $body2, 2, 'gimme');

        $name3 = new Node([new ConstantExpression('container_name', 3)]);
        $parameters3 = new ArrayExpression([new ConstantExpression('foo', 1), new ConstantExpression(true, 1)], 1);
        $body3 = new TextNode('Test body', 3);
        $node3 = new ContainerNode($name3, $parameters3, $body3, 3, 'gimme');

        return [
            [$node1, <<<'EOF'
// line 1
echo "<!-- @deprecated: Container nodes are deprecated from 2.0, will be removed in 3.0 -->";
EOF
            ],
            [$node2, <<<'EOF'
// line 2
echo "<!-- @deprecated: Container nodes are deprecated from 2.0, will be removed in 3.0 -->";
EOF
            ],
            [$node3, <<<'EOF'
// line 3
echo "<!-- @deprecated: Container nodes are deprecated from 2.0, will be removed in 3.0 -->";
EOF
            ],
        ];
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
