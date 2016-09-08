<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Service;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteToArticle;
use SWP\Bundle\ContentBundle\Provider\RouteProvider;

class RouteToArticleMapper implements RouteToArticleMapperInterface
{
    /**
     * @var EntityRepository
     */
    private $routeToArticleRepository;

    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * @var RuleEvaluator
     */
    private $ruleEvaluator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RouteToArticleMapper constructor.
     *
     * @param EntityRepository   $routeToArticleRepository
     * @param DocumentRepository $routeRepository
     * @param LoggerInterface    $logger
     */
    public function __construct(
        EntityRepository $routeToArticleRepository,
        RouteProvider $routeProvider,
        RuleEvaluator $ruleEvaluator,
        LoggerInterface $logger)
    {
        $this->routeToArticleRepository = $routeToArticleRepository;
        $this->routeProvider = $routeProvider;
        $this->ruleEvaluator = $ruleEvaluator;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function assignRouteToArticle(ArticleInterface $article)
    {
        $routeToArticles = $this->routeToArticleRepository->findBy([], ['priority' => 'DESC']);

        /** @var RouteToArticle $routeToArticle */
        foreach ($routeToArticles as $routeToArticle) {
            if ($this->routeMatchesRule($article, $routeToArticle->getRule())) {
                $routeId = $routeToArticle->getRouteId();
                $route = $this->routeProvider->getOneById($routeId);
                if (null !== $route) {
                    $article->setRoute($route);
                    $templateName = $routeToArticle->getTemplateName();
                    if (null !== $templateName) {
                        $article->setTemplateName($templateName);
                    }

                    return true;
                } else {
                    $this->logger->info('No route found with id '.$routeId.' referenced in routeToArticle with id '.$routeToArticle->getId());
                }
            }
        }

        return false;
    }

    /**
     * @param ArticleInterface $article
     * @param $rule
     *
     * @return bool
     */
    private function routeMatchesRule(ArticleInterface $article, $rule)
    {
        $result = false;
        try {
            $result = $this->ruleEvaluator->evaluate($rule, ['article' => $article]);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }

        return $result;
    }
}
