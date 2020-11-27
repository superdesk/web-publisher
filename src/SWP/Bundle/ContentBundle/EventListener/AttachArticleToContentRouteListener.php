<?php


namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;

class AttachArticleToContentRouteListener
{

    /** @var RouteRepositoryInterface  */
    private $routeRepository;

    public function __construct(RouteRepositoryInterface $routeRepository)
    {

        $this->routeRepository = $routeRepository;
    }

    public function onArticlePublish(ArticleEvent $articleEvent): void
    {
        /** @var ArticleInterface $article */
        $article = $articleEvent->getArticle();
        $route = $articleEvent->getArticle()->getRoute();
        $alreadyAttachedRoute = $this->routeRepository->findOneBy(['content' => $article]);

        if($route && !$alreadyAttachedRoute &&  RouteInterface::TYPE_CONTENT === $route->getType()) {
            $route->setContent($article);
            $this->routeRepository->persist($route);
            $this->routeRepository->flush();
        }

    }

    public function onArticleUnpublish(ArticleEvent $articleEvent): void
    {

        $route = $articleEvent->getArticle()->getRoute();

        if($route && RouteInterface::TYPE_CONTENT === $route->getType()) {
            $route->setContent(null);
            $this->routeRepository->persist($route);
            $this->routeRepository->flush();
        }

    }

}