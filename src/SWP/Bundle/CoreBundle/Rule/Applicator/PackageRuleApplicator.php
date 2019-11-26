<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Rule\Applicator;

use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Rule\Populator\ArticlePopulatorInterface;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use SWP\Component\Rule\Applicator\AbstractRuleApplicator;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PackageRuleApplicator extends AbstractRuleApplicator
{
    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var ArticlePopulatorInterface
     */
    private $articlePopulator;

    /**
     * @var array
     */
    private $supportedKeys = ['destinations'];

    public function __construct(TenantRepositoryInterface $tenantRepository, ArticlePopulatorInterface $articlePopulator)
    {
        $this->tenantRepository = $tenantRepository;
        $this->articlePopulator = $articlePopulator;
    }

    public function apply(RuleInterface $rule, RuleSubjectInterface $subject): void
    {
        $configuration = $this->validateRuleConfiguration($rule->getConfiguration());
        if ($subject instanceof PackageInterface && !empty($configuration)) {
            if (ContentInterface::STATUS_CANCELED === $subject->getPubStatus()) {
                return;
            }

            foreach ($configuration[$this->supportedKeys[0]] as $value) {
                if (empty($this->validateDestinationConfig($value))) {
                    return;
                }
            }

            $destinations = [];
            foreach ($configuration[$this->supportedKeys[0]] as $destination) {
                $destinations[] = $this->findTenantByCodeOrThrowException((string) $destination['tenant']);
            }

            $this->articlePopulator->populate($subject, $destinations);
        }
    }

    private function findTenantByCodeOrThrowException(string $code): TenantInterface
    {
        $tenant = $this->tenantRepository->findOneByCode($code);
        if (!$tenant instanceof TenantInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($tenant) ? get_class($tenant) : gettype($tenant),
                TenantInterface::class);
        }

        return $tenant;
    }

    public function isSupported(RuleSubjectInterface $subject): bool
    {
        if (!$subject instanceof PackageInterface && 'package' === $subject->getSubjectType()) {
            $this->logger->warning(sprintf(
                '"%s" is not supported by "%s" rule applicator!',
                is_object($subject) ? get_class($subject) : gettype($subject),
                get_class($this)
            ));

            return false;
        }

        return true;
    }

    private function validateDestinationConfig(array $config): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('tenant');

        return $this->resolveConfig($resolver, $config);
    }

    private function validateRuleConfiguration(array $config): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined($this->supportedKeys[0]);

        return $this->resolveConfig($resolver, $config);
    }
}
