<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UpdatePackageAuthorsListener
{
    private $authorRepository;

    public function __construct(RepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function preUpdate(GenericEvent $event): void
    {
        $package = $event->getSubject();

        if (!$package instanceof PackageInterface) {
            throw new UnexpectedTypeException($package, PackageInterface::class);
        }

        $authors = [];
        foreach ($package->getAuthors()->toArray() as $packageAuthor) {
            if (null !== ($author = $this->authorRepository->findOneBy(['name' => $packageAuthor->getName()]))) {
                $packageAuthor->setId($author->getId());
            }

            $authors[] = $packageAuthor;
        }

        $package->setAuthors(new ArrayCollection($authors));
    }
}
