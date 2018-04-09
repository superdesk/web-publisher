<?php

declare(strict_types=1);

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
