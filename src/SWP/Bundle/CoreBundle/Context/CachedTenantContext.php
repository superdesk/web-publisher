<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Context;

use Symfony\Component\EventDispatcher\GenericEvent;
use function array_key_exists;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\TenantInterface;

class CachedTenantContext extends TenantContext implements CachedTenantContextInterface {
  private $resolvedTenants = [];

  public function getTenant(): ?TenantInterface {
    $currentRequest = $this->requestStack->getCurrentRequest();
    if ($currentRequest && $this->requestStack->getCurrentRequest()->attributes->get('exception') instanceof TenantNotFoundException) {
      return null;
    }
    if (null === $this->tenant && null !== $currentRequest) {
      $cacheKey = self::getCacheKey($currentRequest->getHost());
      if (!array_key_exists($cacheKey, $this->resolvedTenants) || $this->resolvedTenants[$cacheKey] instanceof TenantInterface) {
        $this->resolvedTenants[$cacheKey] = parent::getTenant();
      } else {
        $this->tenant = $this->resolvedTenants[$cacheKey];
      }
    } else {
      return parent::getTenant();
    }

    return $this->tenant;
  }

  public function setTenant(TenantInterface $tenant): void {
    parent::setTenant($tenant);
    $this->dispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);

    $this->resolvedTenants[self::getCacheKey(
        $tenant->getSubdomain() ? $tenant->getSubdomain() . '.' . $tenant->getDomainName() : $tenant->getDomainName()
    )] = $tenant;
  }

  public function reset(): void {
    $this->tenant = null;
    $this->resolvedTenants = [];
  }

  private static function getCacheKey(string $host): string {
    return md5('tenant_cache__' . $host);
  }
}
