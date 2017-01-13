<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Widget;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Bundle\CoreBundle\Widget\ContentListWidget;
use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

final class ContentListWidgetSpec extends ObjectBehavior
{
    public function let(
        WidgetModelInterface $widgetModel,
        ContainerInterface $container
    ) {
        $widgetModel->getParameters()->willReturn(['list_id' => 8]);

        $this->beConstructedWith($widgetModel, $container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListWidget::class);
        $this->shouldHaveType(TemplatingWidgetHandler::class);
    }

    public function it_should_render(
        ContentListRepositoryInterface $contentListRepository,
        ContainerInterface $container,
        EngineInterface $templating,
        ContentListInterface $contentList,
        ContentListItemInterface $contentListItem,
        Response $response
    ) {
        $contentList->getId()->willReturn(8);
        $contentList->getItems()->willReturn(new ArrayCollection([$contentListItem]));

        $container->get('swp.repository.content_list')->willReturn($contentListRepository);
        $container->get('templating')->willReturn($templating);

        $contentListRepository->findListById(8)->willReturn($contentList);
        $templating->render('widgets/list.html.twig', [
            'contentList' => $contentList,
        ])->willReturn($response);

        $this->render()->shouldReturn($response);
    }

    public function it_should_render_with_custom_template(
        ContentListRepositoryInterface $contentListRepository,
        WidgetModelInterface $widgetModel,
        ContainerInterface $container,
        EngineInterface $templating,
        ContentListInterface $contentList,
        ContentListItemInterface $contentListItem,
        Response $response
    ) {
        $widgetModel->getParameters()->willReturn([
            'list_id' => 8,
            'template_name' => 'custom.html.twig',
        ]);

        $this->beConstructedWith($widgetModel, $container);

        $contentList->getId()->willReturn(8);
        $contentList->getItems()->willReturn(new ArrayCollection([$contentListItem]));

        $container->get('swp.repository.content_list')->willReturn($contentListRepository);
        $container->get('templating')->willReturn($templating);

        $contentListRepository->findListById(8)->willReturn($contentList);
        $templating->render('widgets/custom.html.twig', [
            'contentList' => $contentList,
        ])->willReturn($response);

        $this->render()->shouldReturn($response);
    }
}
