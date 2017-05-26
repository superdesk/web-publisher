<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Rule Component.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Rule\Applicator;

use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRuleApplicator implements RuleApplicatorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param OptionsResolver $resolver
     * @param array           $config
     *
     * @return array
     */
    public function resolveConfig(OptionsResolver $resolver, array $config)
    {
        try {
            return $resolver->resolve($config);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return [];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
