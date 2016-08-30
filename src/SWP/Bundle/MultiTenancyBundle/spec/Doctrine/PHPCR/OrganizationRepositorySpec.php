<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\MultiTenancyBundle\Doctrine\ORM;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\OrganizationRepository;
use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;

/**
 * @mixin OrganizationRepository
 */
class OrganizationRepositorySpec extends ObjectBehavior
{
    function let(DocumentManagerInterface $documentManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($documentManager, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationRepository::class);
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(DocumentRepository::class);
        $this->shouldImplement(OrganizationRepositoryInterface::class);
    }
}
