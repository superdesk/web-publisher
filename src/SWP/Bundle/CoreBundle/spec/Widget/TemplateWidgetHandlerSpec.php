<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Widget;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Widget\TemplateWidgetHandler;
use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

final class TemplateWidgetHandlerSpec extends ObjectBehavior
{
    public function let(
        WidgetModelInterface $widgetModel,
        ContainerInterface $container
    ) {
        $this->beConstructedWith($widgetModel, $container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TemplateWidgetHandler::class);
        $this->shouldHaveType(TemplatingWidgetHandler::class);
    }

    public function it_should_render(
        ContainerInterface $container,
        EngineInterface $templating,
        Response $response
    ) {
        $container->get('templating')->willReturn($templating);
        $templating->render('widgets/', ['template_name' => null])->willReturn($response);

        $this->render()->shouldReturn($response);
    }

    public function it_should_render_with_custom_template(
        WidgetModelInterface $widgetModel,
        ContainerInterface $container,
        EngineInterface $templating,
        Response $response
    ) {
        $widgetModel->getParameters()->willReturn([
            'template_name' => 'custom.html.twig',
        ]);

        $this->beConstructedWith($widgetModel, $container);

        $container->get('templating')->willReturn($templating);
        $templating->render('widgets/custom.html.twig', ['template_name' => 'custom.html.twig'])->willReturn($response);

        $this->render()->shouldReturn($response);
    }
}
