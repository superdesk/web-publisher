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

namespace SWP\Bundle\CoreBundle\Adapter;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;

final class CompositeOutputChannelAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    private $adapters = [];

    /**
     * {@inheritdoc}
     */
    public function create(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $adapter = $this->getSupportedAdapter($outputChannel);
        $adapter->create($outputChannel, $article);
    }

    /**
     * {@inheritdoc}
     */
    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $adapter = $this->getSupportedAdapter($outputChannel);
        $adapter->update($outputChannel, $article);
    }

    /**
     * {@inheritdoc}
     */
    public function publish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $adapter = $this->getSupportedAdapter($outputChannel);
        $adapter->publish($outputChannel, $article);
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $adapter = $this->getSupportedAdapter($outputChannel);
        $adapter->unpublish($outputChannel, $article);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OutputChannelInterface $outputChannel): bool
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supports($outputChannel)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AdapterInterface $provider
     */
    public function addAdapter(AdapterInterface $provider): void
    {
        $this->adapters[] = $provider;
    }

    /**
     * @param OutputChannelInterface $outputChannel
     *
     * @throws \InvalidArgumentException
     *
     * @return AdapterInterface
     */
    private function getSupportedAdapter(OutputChannelInterface $outputChannel): AdapterInterface
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supports($outputChannel)) {
                return $adapter;
            }
        }

        throw new \InvalidArgumentException(sprintf('There is no adapter provider registered which supports %s type!', $outputChannel->getType()));
    }
}
