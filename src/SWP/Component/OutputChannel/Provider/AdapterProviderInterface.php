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

interface AdapterProviderInterface
{
    /**
     * @param OutputChannelInterface $outputChannel
     *
     * @return AdapterInterface
     */
    public function get(OutputChannelInterface $outputChannel): AdapterInterface;

    /**
     * @param OutputChannelInterface $outputChannel
     *
     * @return bool
     */
    public function supports(OutputChannelInterface $outputChannel): bool;
}
