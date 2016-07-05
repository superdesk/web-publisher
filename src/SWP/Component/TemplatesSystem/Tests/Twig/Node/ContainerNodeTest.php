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

use SWP\Component\TemplatesSystem\Twig\Node\ContainerNode;

class ContainerNodeTest extends \Twig_Test_NodeTestCase
{
    public function testConstructor()
    {
        $name = new \Twig_Node([new \Twig_Node_Expression_Constant('container_name', 1)]);
        $parameters = new \Twig_Node_Expression_Array([], 1);
        $body = new \Twig_Node_Text('', 1);
        $node = new ContainerNode($name, $parameters, $body, 1, 'gimme');
        $this->assertEquals($name, $node->getNode('name'));
        $this->assertEquals($parameters, $node->getNode('parameters'));
        $this->assertEquals($body, $node->getNode('body'));
    }

    public function getTests()
    {
        $name1 = new \Twig_Node([new \Twig_Node_Expression_Constant('container_name', 1)]);
        $parameters1 = new \Twig_Node_Expression_Array([], 1);
        $body1 = new \Twig_Node_Text('Test body', 1);
        $node1 = new ContainerNode($name1, $parameters1, $body1, 1, 'gimme');

        $name2 = new \Twig_Node([new \Twig_Node_Expression_Constant('container_name', 2)]);
        $body2 = new \Twig_Node_Text('Test body', 2);
        $node2 = new ContainerNode($name2, null, $body2, 2, 'gimme');

        $name3 = new \Twig_Node([new \Twig_Node_Expression_Constant('container_name', 3)]);
        $parameters3 = new \Twig_Node_Expression_Array([new \Twig_Node_Expression_Constant('foo', 1), new \Twig_Node_Expression_Constant(true, 1)], 1);
        $body3 = new \Twig_Node_Text('Test body', 3);
        $node3 = new ContainerNode($name3, $parameters3, $body3, 3, 'gimme');

        return [
            [$node1, <<<'EOF'
// line 1
$containerService = $this->env->getExtension('swp_container')->getContainerService();
$container = $containerService->getContainer("container_name", array());
if ($container->isVisible()) {
    echo $container->renderOpenTag();
    if ($container->hasWidgets()) {
        echo $container->renderWidgets();
    } else {
        echo "Test body";
    }
    echo $container->renderCloseTag();
}
unset($container);unset($containerService);
EOF
            ],
            [$node2, <<<'EOF'
// line 2
$containerService = $this->env->getExtension('swp_container')->getContainerService();
$container = $containerService->getContainer("container_name", array());
if ($container->isVisible()) {
    echo $container->renderOpenTag();
    if ($container->hasWidgets()) {
        echo $container->renderWidgets();
    } else {
        echo "Test body";
    }
    echo $container->renderCloseTag();
}
unset($container);unset($containerService);
EOF
            ],
            [$node3, <<<'EOF'
// line 3
$containerService = $this->env->getExtension('swp_container')->getContainerService();
$container = $containerService->getContainer("container_name", array("foo" => true));
if ($container->isVisible()) {
    echo $container->renderOpenTag();
    if ($container->hasWidgets()) {
        echo $container->renderWidgets();
    } else {
        echo "Test body";
    }
    echo $container->renderCloseTag();
}
unset($container);unset($containerService);
EOF
            ],
        ];
    }
}
