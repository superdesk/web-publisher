<?php


namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;

class AttachArticleToContentRouteListener
{

    private RouteRepositoryInterface $routeRepository;

    public function __construct(RouteRepositoryInterface $routeRepository)
    {

        $this->routeRepository = $routeRepository;
    }

    public function onArticlePublish(ArticleEvent $articleEvent): void
    {
        /** @var ArticleInterface $article */
        $article = $articleEvent->getArticle();
        $route = $articleEvent->getArticle()->getRoute();

        if(RouteInterface::TYPE_CONTENT === $route->getType()) {
            $route->setContent($article);
            $this->routeRepository->persist($route);
            $this->routeRepository->flush();
        }

    }

    public function onArticleUnpublish(ArticleEvent $articleEvent): void
    {

        $route = $articleEvent->getArticle()->getRoute();

        if(RouteInterface::TYPE_CONTENT === $route->getType()) {
            $route->setContent(null);
            $this->routeRepository->persist($route);
            $this->routeRepository->flush();
        }

    }

}