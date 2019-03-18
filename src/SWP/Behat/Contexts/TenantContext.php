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
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class TenantContext extends AbstractContext implements Context
{
    private $tenantContext;

    private $organizationFactory;

    private $tenantFactory;

    private $tenantRepository;

    private $entityManager;

    public function __construct(
        TenantContextInterface $tenantContext,
        OrganizationFactoryInterface $organizationFactory,
        TenantFactoryInterface $tenantFactory,
        TenantRepositoryInterface $tenantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->tenantContext = $tenantContext;
        $this->organizationFactory = $organizationFactory;
        $this->tenantFactory = $tenantFactory;
        $this->tenantRepository = $tenantRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given the following Tenants:
     */
    public function theFollowingTenants(TableNode $table)
    {
        $currentTenant = null;

        foreach ($table as $row => $columns) {
            $tenant = $this->tenantFactory->create();
            $this->entityManager->persist($tenant);

            /** @var OrganizationInterface $organization */
            $organization = $this->organizationFactory->create();
            $organization->setName($columns['organization']);
            $organization->setCode('123456');
            $this->entityManager->persist($organization);
            $columns['organization'] = $organization;

            if ('null' === $columns['subdomain'] || 0 === strlen(trim($columns['subdomain']))) {
                $columns['subdomain'] = null;
            }
            $columns['enabled'] = (bool) $columns['enabled'];

            if (true === (bool) $columns['default']) {
                $currentTenant = $tenant;
            }
            unset($columns['default']);

            $this->fillObject($tenant, $columns);
        }

        $this->entityManager->flush();
        $this->tenantContext->setTenant($currentTenant);
    }
}
