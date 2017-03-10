<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\TenantRepository;
use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

/**
 * @mixin TenantRepository
 */
class TenantRepositorySpec extends ObjectBehavior
{
    public function let(DocumentManagerInterface $documentManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($documentManager, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantRepository::class);
    }

    public function it_is_a_repository()
    {
        $this->shouldHaveType(DocumentRepository::class);
        $this->shouldImplement(TenantRepositoryInterface::class);
    }
}
