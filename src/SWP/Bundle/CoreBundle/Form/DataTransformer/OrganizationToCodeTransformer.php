<?php

/**
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

namespace SWP\Bundle\CoreBundle\Form\DataTransformer;

use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class OrganizationToCodeTransformer implements DataTransformerInterface
{
    /**
     * @var OrganizationRepositoryInterface
     */
    private $organizationRepository;

    /**
     * OrganizationToCodeTransformer constructor.
     *
     * @param OrganizationRepositoryInterface $organizationRepository
     */
    public function __construct(OrganizationRepositoryInterface $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Transforms an object (organization) to a string (code).
     *
     * @param OrganizationInterface|null $organization
     *
     * @return string
     */
    public function transform($organization)
    {
        if (null === $organization) {
            return '';
        }

        if (!$organization instanceof OrganizationInterface) {
            throw new UnexpectedTypeException($organization, OrganizationInterface::class);
        }

        return $organization->getCode();
    }

    /**
     * Transforms a string (code) to an object (organization).
     *
     * @param string $organizationCode
     *
     * @return OrganizationInterface|null
     *
     * @throws TransformationFailedException if object (organization) is not found
     */
    public function reverseTransform($organizationCode)
    {
        if (null === $organizationCode || '' === $organizationCode) {
            return;
        }

        $organization = $this->organizationRepository->findOneByCode($organizationCode);

        if (null === $organization) {
            throw new TransformationFailedException(sprintf(
                'An organization with code "%s" does not exist!',
                $organizationCode
            ));
        }

        return $organization;
    }
}
