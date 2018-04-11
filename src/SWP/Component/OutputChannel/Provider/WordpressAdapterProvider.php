<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Output Channel Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\OutputChannel\Provider;

use SWP\Component\OutputChannel\Adapter\AdapterInterface;
use SWP\Component\OutputChannel\Model\OutputChannelInterface;

final class WordpressAdapterProvider implements AdapterProviderInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * WordpressAdapterProvider constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function get(OutputChannelInterface $outputChannel): AdapterInterface
    {
        $outputChannelConfig = $outputChannel->getConfig();

        $this->adapter->setConfig($outputChannelConfig);

        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OutputChannelInterface $outputChannel): bool
    {
        return OutputChannelInterface::TYPE_WORDPRESS === $outputChannel->getType();
    }
}
