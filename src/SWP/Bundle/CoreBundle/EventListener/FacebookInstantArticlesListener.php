<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Facebook\InstantArticles\Client\InstantArticleStatus;
use SWP\Bundle\CoreBundle\Event\ContentListEvent;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookInstantArticlesManagerInterface;
use SWP\Bundle\FacebookInstantArticlesBundle\Parser\TemplateParser;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;

class FacebookInstantArticlesListener
{
    public function __construct(
        TemplateParser $templateParser,
        MetaFactory $metaFactory,
        FactoryInterface $instantArticlesArticleFactory,
        EntityRepository $feedRepository,
        FacebookInstantArticlesManagerInterface $facebookInstantArticlesManager
    ) {
        $this->templateParser = $templateParser;
        $this->metaFactory = $metaFactory;
        $this->instantArticlesArticleFactory = $instantArticlesArticleFactory;
        $this->feedRepository = $feedRepository;
        $this->facebookInstantArticlesManager = $facebookInstantArticlesManager;
    }

    /**
     * @param ContentListEvent $event
     */
    public function sendArticleToFacebook(ContentListEvent $event)
    {
        $feeds = $this->feedRepository->getQueryByCriteria(new Criteria([
            'contentList' => $event->getContentList(),
        ]), [], 'f')->getQuery()->getResult();

        if (count($feeds) === 0) {
            return;
        }

        $article = $event->getItem()->getContent();
        $this->metaFactory->create($article);
        $instantArticle = $this->templateParser->parse();

        foreach ($feeds as $feed) {
            $submissionId = $this->facebookInstantArticlesManager->sendArticleToFacebook($feed, $instantArticle);

            /** @var FacebookInstantArticlesArticle $instantArticleEntity */
            $instantArticleEntity = $this->instantArticlesArticleFactory->create();
            $instantArticleEntity->setSubmissionId($submissionId);
            $instantArticleEntity->setArticle($article);
            $instantArticleEntity->setFeed($feed);
            $instantArticleEntity->setStatus('new');
            $this->feedRepository->add($instantArticleEntity);

            // Wait for processing article by Facebook
            sleep(5);

            $submissionStatus = $this->facebookInstantArticlesManager->getSubmissionStatus($feed, $submissionId);
            $instantArticleEntity->setStatus($submissionStatus->getStatus());
            $instantArticleEntity->setErrors($this->getSubmissionErrors($submissionStatus));

            dump($instantArticleEntity, $submissionStatus);
        }
    }

    /**
     * @param InstantArticleStatus $submissionStatus
     *
     * @return array
     */
    private function getSubmissionErrors(InstantArticleStatus $submissionStatus): array
    {
        $errors = [];
        foreach ($submissionStatus->getMessages() as $serverMessage) {
            $errors[] = [$serverMessage->getLevel() => $serverMessage->getMessage()];
        }

        return $errors;
    }
}
