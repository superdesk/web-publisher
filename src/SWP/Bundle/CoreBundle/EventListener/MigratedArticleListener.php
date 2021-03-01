<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleExtraTextField;
use SWP\Bundle\CoreBundle\Model\RedirectRouteInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class MigratedArticleListener
{
    private $redirectRouteFactory;

    private $entityManager;

    private $router;

    public function __construct(
        FactoryInterface $redirectRouteFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->redirectRouteFactory = $redirectRouteFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function publish(ArticleEvent $articleEvent): void
    {
        $article = $articleEvent->getArticle();
        $originalArticleUrlExtra = $article->getExtraByKey('original_article_url');
        if ($originalArticleUrlExtra &&
            $originalArticleUrlExtra instanceof ArticleExtraTextField &&
            is_string($originalArticleUrlExtra->getValue())) {
            $urlParts = parse_url($originalArticleUrlExtra->getValue());
            if (isset($urlParts['path'])) {
                /** @var RedirectRouteInterface $redirectRoute */
                $redirectRoute = $this->redirectRouteFactory->create();
                $redirectRoute->setUri($this->router->generate($article));
                $redirectRoute->setPermanent(true);
                $redirectRoute->setStaticPrefix($urlParts['path']);
                $redirectRoute->setRouteName($urlParts['path']);

                $this->entityManager->persist($redirectRoute);
                $this->entityManager->flush();
            }
        }
    }
}
