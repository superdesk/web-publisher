<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Document\Article;
use SWP\Bundle\ContentBundle\Document\Route;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadSeparateArticlesData extends AbstractFixture implements FixtureInterface
{
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $env = $this->getEnvironment();

        $this->loadArticles($env, $manager);

        $manager->flush();
    }

    /**
     * Sets articles manually (not via Alice) for test env due to fatal error:
     * Method PHPCRProxies\__CG__\Doctrine\ODM\PHPCR\Document\Generic::__toString() must not throw an exception.
     */
    public function loadArticles($env, $manager)
    {
        if ($env !== 'test') {
            $this->loadFixtures(
                '@SWPFixturesBundle/Resources/fixtures/PHPCR/'.$env.'/article.yml',
                $manager,
                [
                    'providers' => [$this],
                ]
            );
        }

        $articles = [
            'test' => [
                [
                    'title'   => 'Test news article',
                    'content' => 'Test news article content',
                    'parent'  => '/swp/default/content',
                ],
                [
                    'title'   => 'Test content article',
                    'content' => 'Test article content',
                    'parent'  => '/swp/default/content',
                ],
            ],
        ];

        if (isset($articles[$env])) {
            foreach ($articles[$env] as $articleData) {
                $article = new Article();
                $article->setParent($manager->find(null, $articleData['parent']));
                $article->setTitle($articleData['title']);
                $article->setContent($articleData['content']);
                if (isset($articleData['route'])) {
                    $article->setRoute($manager->find(null, $articleData['route']));
                }

                $manager->persist($article);
            }

            $manager->flush();
        }
    }
}
