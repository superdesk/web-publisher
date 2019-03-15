<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleService as BaseArticleService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticleService extends BaseArticleService
{
    private $dateTimeService;

    public function __construct(EventDispatcherInterface $eventDispatcher, DateTimeService $dateTimeService)
    {
        parent::__construct($eventDispatcher);

        $this->dateTimeService = $dateTimeService;
    }

    public function publish(ArticleInterface $article): ArticleInterface
    {
        if (null !== ($dateTime = $this->dateTimeService->getCurrentDateTime())) {
            $article->setPublishedAt($dateTime);
        }

        return parent::publish($article);
    }
}
