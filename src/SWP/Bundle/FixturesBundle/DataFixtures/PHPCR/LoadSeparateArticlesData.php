<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadSeparateArticlesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $manager;
    private $defaultTenantPrefix;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $env = $this->getEnvironment();

        $this->defaultTenantPrefix = $this->getTenantPrefix();

        $this->loadArticles($env, $manager);

        $manager->flush();
    }

    /**
     * Sets articles manually (not via Alice) for test env due to fatal error:
     * Method PHPCRProxies\__CG__\Doctrine\ODM\PHPCR\Document\Generic::__toString() must not throw an exception.
     */
    public function loadArticles($env, $manager)
    {
        $articles = [
            'test' => [
                [
                    'title' => 'Test news article',
                    'content' => 'Test news article content',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Test content article',
                    'content' => 'Test article content',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
            ],
        ];

        if (isset($articles[$env])) {
            foreach ($articles[$env] as $articleData) {
                $article = new Article();
                $article->setParent($manager->find(null, $articleData['parent']));
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setLocale($articleData['locale']);
                $article->setPublishedAt(new \DateTime());
                $article->setPublishable(true);
                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);

                $manager->persist($article);
            }

            $manager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
