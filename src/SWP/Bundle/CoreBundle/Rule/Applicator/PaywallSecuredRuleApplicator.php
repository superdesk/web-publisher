<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Rule\Applicator;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Rule\Applicator\AbstractRuleApplicator;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Model\RuleInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaywallSecuredRuleApplicator extends AbstractRuleApplicator
{
    /**
     * @var array
     */
    private $supportedKeys = ['paywallSecured'];

    public function apply(RuleInterface $rule, RuleSubjectInterface $subject): void
    {
        $configuration = $this->validateRuleConfiguration($rule->getConfiguration());

        if (empty($configuration) || !$this->isAllowedType($subject)) {
            return;
        }

        if ($isPaywallSecured = (bool) $configuration['paywallSecured']) {
            $subject->setPaywallSecured($isPaywallSecured);

            $this->logger->info(sprintf(
                'Configuration: paywallSecured for "%s" rule has been applied!',
                $rule->getExpression()
            ));
        }
    }

    public function isSupported(RuleSubjectInterface $subject): bool
    {
        return $subject instanceof ArticleInterface && 'article' === $subject->getSubjectType();
    }

    private function validateRuleConfiguration(array $configuration): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver, $configuration);

        return $this->resolveConfig($resolver, $configuration);
    }

    private function configureOptions(OptionsResolver $resolver, array $configuration): void
    {
        $resolver->setDefaults([
            $this->supportedKeys[0] => false,
        ]);
        $resolver->setDefined(array_keys($configuration));
    }

    private function isAllowedType(RuleSubjectInterface $subject): bool
    {
        if (!$subject instanceof ArticleInterface) {
            $this->logger->warning(sprintf(
                '"%s" is not supported by "%s" rule applicator!',
                is_object($subject) ? get_class($subject) : gettype($subject),
                get_class($this)
            ));

            return false;
        }

        return true;
    }
}
