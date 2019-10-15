<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\CoreBundle\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Factory\OrganizationFactoryInterface;
use SWP\Component\MultiTenancy\Factory\TenantFactoryInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class TenantContext extends AbstractContext implements Context
{
    private $tenantContext;

    private $organizationFactory;

    private $tenantFactory;

    private $tenantRepository;

    private $entityManager;

    private $organizationRepository;

    public function __construct(
        TenantContextInterface $tenantContext,
        OrganizationFactoryInterface $organizationFactory,
        TenantFactoryInterface $tenantFactory,
        TenantRepositoryInterface $tenantRepository,
        OrganizationRepositoryInterface $organizationRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->tenantContext = $tenantContext;
        $this->organizationFactory = $organizationFactory;
        $this->tenantFactory = $tenantFactory;
        $this->tenantRepository = $tenantRepository;
        $this->entityManager = $entityManager;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @Given the following Tenants:
     */
    public function theFollowingTenants(TableNode $table)
    {
        $currentTenant = null;
        $organizations = [];

        foreach ($table as $row => $columns) {
            if (array_key_exists('code', $columns)) {
                $existingTenant = $this->tenantRepository->findOneByCode($columns['code']);
                if (null !== $existingTenant) {
                    $currentTenant = $this->setCurrentTenant($columns, $existingTenant);

                    continue;
                }
            }

            if (array_key_exists('code', $columns)) {
                $tenant = $this->tenantFactory->createWithoutCode();
            } else {
                $tenant = $this->tenantFactory->create();
            }
            $this->entityManager->persist($tenant);

            $organization = $this->organizationRepository->findOneByName($columns['organization']);
            if (array_key_exists($columns['organization'], $organizations)) {
                $organization = $organizations[$columns['organization']];
            }

            if (null === $organization) {
                /** @var OrganizationInterface $organization */
                $organization = $this->organizationFactory->create();
                $organization->setName($columns['organization']);
                $organization->setCode('123456');
                $this->entityManager->persist($organization);
                $organizations[$organization->getName()] = $organization;
            }
            $columns['organization'] = $organization;

            if ('null' === $columns['subdomain'] || 0 === strlen(trim($columns['subdomain']))) {
                $columns['subdomain'] = null;
            }
            $columns['enabled'] = (bool) $columns['enabled'];

            $currentTenant = $this->setCurrentTenant($columns, $tenant);

            $this->fillObject($tenant, $columns);
        }

        $this->entityManager->flush();
        $this->tenantContext->setTenant($currentTenant);
    }

    /**
     * @Given default tenant with code :code
     */
    public function defaultTenantWithCode($code)
    {
        $tenant = $this->tenantRepository->findOneByCode($code);
        if (null === $tenant) {
            throw new \Exception('Tenant was not found');
        }

        $this->tenantContext->setTenant($tenant);
    }

    private function setCurrentTenant(&$columns, $tenant)
    {
        $currentTenant = null;
        if (true === (bool) $columns['default']) {
            $currentTenant = $tenant;
        }
        unset($columns['default']);

        return $currentTenant;
    }
}
