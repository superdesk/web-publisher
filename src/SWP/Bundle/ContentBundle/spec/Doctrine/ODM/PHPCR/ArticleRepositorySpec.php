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
namespace spec\SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleRepository;
use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;

/**
 * @mixin ArticleRepository
 */
class ArticleRepositorySpec extends ObjectBehavior
{
    public function let(DocumentManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleRepository::class);
    }

    public function it_is_repository()
    {
        $this->shouldHaveType(DocumentRepository::class);
        $this->shouldImplement(ArticleRepositoryInterface::class);
    }
}
