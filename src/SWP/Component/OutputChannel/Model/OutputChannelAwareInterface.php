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

namespace SWP\Component\OutputChannel\Model;

interface OutputChannelAwareInterface
{
    /**
     * @return OutputChannelInterface|null
     */
    public function getOutputChannel(): ?OutputChannelInterface;

    /**
     * @param OutputChannelInterface|null $outputChannel
     */
    public function setOutputChannel(?OutputChannelInterface $outputChannel): void;
}
