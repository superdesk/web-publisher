<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\KeyGenerator;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use SWP\Bundle\ContentBundle\KeyGenerator\MetaKeyGenerator;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\FixturesBundle\WebTestCase;

/**
 * Class MetaKeyGeneratorTest.
 */
class MetaKeyGeneratorTest extends WebTestCase
{
    /**
     * test key generation.
     */
    public function testGenerateKey()
    {
        $keyGenerator = new MetaKeyGenerator();
        $metaFactory = $this->getContainer()->get('swp_template_engine_context.factory.meta_factory');

        $key = $keyGenerator->generateKey('some string');
        self::assertEquals(sha1(serialize('some string')), $key);

        $article = new Article();
        $articleMeta = $metaFactory->create($article);
        $key = $keyGenerator->generateKey($articleMeta);
        $date = null !== $article->getUpdatedAt() ? $article->getUpdatedAt() : $article->getCreatedAt();
        self::assertEquals(sha1(implode('', [$date->getTimestamp(), $article->getId()])), $key);

        $route = new Route();
        $route->setId('route-name');
        $routeMeta = $metaFactory->create($route);
        $key = $keyGenerator->generateKey($routeMeta);
        self::assertEquals(sha1(implode('', [$route->getId()])), $key);
    }
}
