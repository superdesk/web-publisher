<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * @mixin DocumentRepository
 */
class DocumentRepositorySpec extends ObjectBehavior
{
    public function let(DocumentManagerInterface $manager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($manager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DocumentRepository::class);
    }

    public function it_is_repository()
    {
        $this->shouldHaveType(DocumentRepository::class);
        $this->shouldImplement(RepositoryInterface::class);
    }
}
